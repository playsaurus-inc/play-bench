<?php

namespace App\Http\Controllers;

use App\Models\AiModel;
use App\Models\RpsMatch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AiModelController extends Controller
{
    /**
     * Display a listing of the AI models with their RPS performance.
     */
    public function index(): View
    {
        $models = AiModel::withCount([
                'rpsMatchesAsPlayer1',
                'rpsMatchesAsPlayer2',
                'rpsMatchesWon',
            ])
            ->get()
            ->map(function ($model) {
                $model->total_rps_matches = $model->rps_matches_as_player1_count + $model->rps_matches_as_player2_count;
                $model->win_rate = $model->total_rps_matches > 0
                    ? $model->rps_matches_won_count / $model->total_rps_matches
                    : 0;

                return $model;
            })
            ->sortByDesc('win_rate');

        // Get top 3 models by win rate (with minimum matches)
        $topModels = $models->filter(function ($model) {
            return $model->total_rps_matches >= 5;
        })->take(3);

        // Get statistics for the entire benchmark
        $benchmarkStats = [
            'total_models' => $models->count(),
            'total_matches' => RpsMatch::count(),
            'avg_win_rate' => $models->where('total_rps_matches', '>', 0)->avg('win_rate') * 100,
        ];

        return view('models.index', [
            'models' => $models,
            'topModels' => $topModels,
            'benchmarkStats' => $benchmarkStats,
        ]);
    }

    /**
     * Display the specified AI model and its performance details.
     */
    public function show(AiModel $aiModel): View
    {
        // Load RPS matches for this model
        $rpsMatches = RpsMatch::where(function (Builder $query) use ($aiModel) {
                $query->where('player1_id', $aiModel->id)
                    ->orWhere('player2_id', $aiModel->id);
            })
            ->with(['player1', 'player2', 'winner'])
            ->latest()
            ->paginate(6);

        // Calculate performance statistics
        $totalRpsMatches = $aiModel->rpsMatchesAsPlayer1()->count() + $aiModel->rpsMatchesAsPlayer2()->count();
        $totalRpsWins = $aiModel->rpsMatchesWon()->count();
        $winRate = $totalRpsMatches > 0 ? $totalRpsWins / $totalRpsMatches : 0;

        // Get opponent win rates
        $opponents = AiModel::whereHas('rpsMatchesAsPlayer1', function (Builder $query) use ($aiModel) {
                $query->where('player2_id', $aiModel->id);
            })
            ->orWhereHas('rpsMatchesAsPlayer2', function (Builder $query) use ($aiModel) {
                $query->where('player1_id', $aiModel->id);
            })
            ->get()
            ->map(function ($opponent) use ($aiModel) {
                // Calculate win rate against this specific opponent
                $matchesAgainstOpponent = RpsMatch::where(function (Builder $query) use ($aiModel, $opponent) {
                        $query->where('player1_id', $aiModel->id)->where('player2_id', $opponent->id)
                            ->orWhere('player1_id', $opponent->id)->where('player2_id', $aiModel->id);
                    })
                    ->get();

                $winsAgainstOpponent = $matchesAgainstOpponent->filter(function ($match) use ($aiModel) {
                    return $match->winner_id === $aiModel->id;
                })->count();

                $totalMatchesAgainstOpponent = $matchesAgainstOpponent->count();
                $opponent->win_rate_against = $totalMatchesAgainstOpponent > 0
                    ? $winsAgainstOpponent / $totalMatchesAgainstOpponent
                    : 0;
                $opponent->total_matches_against = $totalMatchesAgainstOpponent;

                return $opponent;
            });

        // Calculate move tendencies
        $moveBreakdown = [
            'rock' => 0,
            'paper' => 0,
            'scissors' => 0,
        ];
        $totalMoves = 0;
        $consecutiveMoves = [
            'rock_to_rock' => 0,
            'rock_to_paper' => 0,
            'rock_to_scissors' => 0,
            'paper_to_rock' => 0,
            'paper_to_paper' => 0,
            'paper_to_scissors' => 0,
            'scissors_to_rock' => 0,
            'scissors_to_paper' => 0,
            'scissors_to_scissors' => 0,
        ];
        $totalConsecutiveMoves = 0;

        foreach ($rpsMatches as $match) {
            $rounds = $match->getRounds();
            $previousMove = null;

            foreach ($rounds as $round) {
                $move = null;

                if ($match->player1_id === $aiModel->id) {
                    $move = $round['player1_move'];
                    if (isset($moveBreakdown[$move])) {
                        $moveBreakdown[$move]++;
                        $totalMoves++;

                        if ($previousMove !== null) {
                            $key = "{$previousMove}_to_{$move}";
                            if (isset($consecutiveMoves[$key])) {
                                $consecutiveMoves[$key]++;
                                $totalConsecutiveMoves++;
                            }
                        }

                        $previousMove = $move;
                    }
                } else {
                    $move = $round['player2_move'];
                    if (isset($moveBreakdown[$move])) {
                        $moveBreakdown[$move]++;
                        $totalMoves++;

                        if ($previousMove !== null) {
                            $key = "{$previousMove}_to_{$move}";
                            if (isset($consecutiveMoves[$key])) {
                                $consecutiveMoves[$key]++;
                                $totalConsecutiveMoves++;
                            }
                        }

                        $previousMove = $move;
                    }
                }
            }
        }

        // Get most impressive victory (highest point difference)
        $mostImpressiveVictory = RpsMatch::where('winner_id', $aiModel->id)
            ->where(function (Builder $query) use ($aiModel) {
                $query->where('player1_id', $aiModel->id)
                    ->orWhere('player2_id', $aiModel->id);
            })
            ->with(['player1', 'player2'])
            ->orderByRaw('ABS(player1_score - player2_score) DESC')
            ->first();

        // Get rankings information
        $ranking = AiModel::withCount([
                'rpsMatchesAsPlayer1',
                'rpsMatchesAsPlayer2',
                'rpsMatchesWon',
            ])
            ->get()
            ->map(function ($model) {
                $model->total_rps_matches = $model->rps_matches_as_player1_count + $model->rps_matches_as_player2_count;
                $model->win_rate = $model->total_rps_matches > 0
                    ? $model->rps_matches_won_count / $model->total_rps_matches
                    : 0;
                return $model;
            })
            ->sortByDesc('win_rate');

        $rankPosition = $ranking->search(function($rankedModel) use ($aiModel) {
            return $rankedModel->id === $aiModel->id;
        }) + 1;

        return view('models.show', compact(
            'aiModel',
            'rpsMatches',
            'winRate',
            'totalRpsMatches',
            'totalRpsWins',
            'opponents',
            'moveBreakdown',
            'totalMoves',
            'consecutiveMoves',
            'totalConsecutiveMoves',
            'mostImpressiveVictory',
            'rankPosition'
        ));
    }
}
