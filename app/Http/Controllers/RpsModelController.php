<?php

namespace App\Http\Controllers;

use App\Models\AiModel;
use App\Models\RpsMatch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RpsModelController extends Controller
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

        return view('rps.models.index', [
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
        $rpsMatches = RpsMatch::query()
            ->playedBy($aiModel)
            ->with(['player1', 'player2', 'winner'])
            ->latest()
            ->take(6)
            ->get();

        // Calculate performance statistics
        $totalRpsMatches = $aiModel->rpsMatches()->count();
        $totalRpsWins = $aiModel->rpsMatchesWon()->count();
        $winRate = $totalRpsMatches > 0 ? $totalRpsWins / $totalRpsMatches : 0;

        // Get opponent win rates
        $opponents = $this->oponents($aiModel);

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

        $mostImpressiveVictory = $this->mostImpressiveVictory($aiModel);

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

        return view('rps.models.show', [
            'aiModel' => $aiModel,
            'rpsMatches' => $rpsMatches,
            'winRate' => $winRate,
            'totalRpsMatches' => $totalRpsMatches,
            'totalRpsWins' => $totalRpsWins,
            'opponents' => $opponents,
            'moveBreakdown' => $moveBreakdown,
            'totalMoves' => $totalMoves,
            'consecutiveMoves' => $consecutiveMoves,
            'totalConsecutiveMoves' => $totalConsecutiveMoves,
            'mostImpressiveVictory' => $mostImpressiveVictory,
            'rankPosition' => $rankPosition,
        ]);
    }

    /**
     * Get all opponents of the given AI model and their win rates.
     */
    protected function oponents(AiModel $aiModel)
    {
        return AiModel::query()
            ->whereHas('rpsMatchesAsPlayer1', fn ($q) => $q->where('player2_id', $aiModel->id))
            ->orWhereHas('rpsMatchesAsPlayer2', fn ($q) => $q->where('player1_id', $aiModel->id))
            ->with([
                'rpsMatchesAsPlayer1' => fn ($q) => $q->where('player2_id', $aiModel->id),
                'rpsMatchesAsPlayer2' => fn ($q) => $q->where('player1_id', $aiModel->id),
            ])
            ->get()
            ->map(function (AiModel $opponent) use ($aiModel) {
                $matches = $opponent->rpsMatchesAsPlayer1->concat($opponent->rpsMatchesAsPlayer2);
                $wins = $matches->where('winner_id', $aiModel->id)->count();
                $total = $matches->count();

                return (object)[
                    'model' => $opponent,
                    'win_rate' => $total > 0 ? $wins / $total : 0,
                    'total_matches' => $total,
                ];
            });
    }

    /**
     * Get the most impressive victory of the given AI model.
     * This is the match with the highest point difference.
     */
    protected function mostImpressiveVictory(AiModel $aiModel): RpsMatch
    {
        return RpsMatch::query()
            ->wonBy($aiModel)
            ->with(['player1', 'player2'])
            ->orderByRaw('ABS(player1_score - player2_score) DESC')
            ->first();
    }
}
