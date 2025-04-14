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

        return view('models.index', compact('models'));
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
            ->paginate(10);

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

                return $opponent;
            });

        return view('models.show', compact('aiModel', 'rpsMatches', 'winRate', 'totalRpsMatches', 'totalRpsWins', 'opponents'));
    }
}
