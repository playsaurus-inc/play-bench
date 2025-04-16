<?php

namespace App\Http\Controllers;

use App\Models\AiModel;
use App\Models\RpsMatch;
use App\Models\ChessMatch;
use App\Models\SvgMatch;
use Illuminate\Http\Request;
use Illuminate\Support\Number;
use Illuminate\View\View;

class ModelController extends Controller
{
    /**
     * Display a listing of all AI models across all benchmarks.
     */
    public function index(): View
    {
        // Get all models with their rankings
        $models = AiModel::query()
            ->withCount([
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
            ->sortByDesc('rps_elo');

        // Overall stats
        $modelCount = $models->count();
        $matchCount = RpsMatch::count() + ChessMatch::count() + SvgMatch::count();
        $rpsMatchCount = RpsMatch::count();

        // For now, we only have Rock Paper Scissors, but in the future we'll add more
        $benchmarkCount = 1; // Will become 3 when Chess and SVG are implemented

        return view('models.index', [
            'models' => $models,
            'modelCount' => $modelCount,
            'matchCount' => $matchCount,
            'rpsMatchCount' => $rpsMatchCount,
            'benchmarkCount' => $benchmarkCount,
        ]);
    }

    /**
     * Display the specified model with performance across all benchmarks.
     */
    public function show(AiModel $aiModel): View
    {
        // Load RPS matches for this model
        $rpsMatches = RpsMatch::query()
            ->playedBy($aiModel)
            ->with(['player1', 'player2', 'winner'])
            ->latest()
            ->take(4)
            ->get();

        // In the future, we would also load chess matches and SVG matches here

        return view('models.show', [
            'model' => $aiModel,
            'rpsMatches' => $rpsMatches,
        ]);
    }
}
