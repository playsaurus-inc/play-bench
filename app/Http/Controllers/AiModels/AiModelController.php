<?php

namespace App\Http\Controllers\AiModels;

use App\Http\Controllers\Controller;
use App\Models\AiModel;
use App\Models\ChessMatch;
use App\Models\RpsMatch;
use App\Models\SvgMatch;
use Illuminate\View\View;

class AiModelController extends Controller
{
    /**
     * Display a listing of all AI models across all benchmarks.
     */
    public function index(): View
    {
        // Get all models with their rankings
        $models = AiModel::all()->sortByDesc('elo');

        // Overall stats
        $modelCount = $models->count();
        $rpsMatchCount = RpsMatch::count();
        $svgMatchCount = SvgMatch::count();
        $chessMatchCount = 0; //ChessMatch::count();

        return view('models.index', [
            'models' => $models,
            'modelCount' => $modelCount,
            'matchCount' => $rpsMatchCount + $svgMatchCount + $chessMatchCount,
            'rpsMatchCount' => $rpsMatchCount,
            'svgMatchCount' => $svgMatchCount,
            'chessMatchCount' => $chessMatchCount,
            'benchmarkCount' => 2, // RPS and SVG
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

        // Calculate RPS stats
        $totalRpsMatches = $aiModel->rpsMatches()->count();
        $totalRpsWins = $aiModel->rpsMatchesWon()->count();
        $rpsWinRate = $totalRpsMatches > 0 ? $totalRpsWins / $totalRpsMatches : 0;

        // In the future, we would also load chess matches and SVG matches here

        return view('models.show', [
            'model' => $aiModel,
            'rpsMatches' => $rpsMatches,
            'totalRpsMatches' => $totalRpsMatches,
            'totalRpsWins' => $totalRpsWins,
            'rpsWinRate' => $rpsWinRate,
            'activeTab' => 'overview',
        ]);
    }
}
