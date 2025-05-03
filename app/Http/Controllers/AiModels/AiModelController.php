<?php

namespace App\Http\Controllers\AiModels;

use App\Http\Controllers\Controller;
use App\Models\AiModel;
use App\Models\ChessMatch;
use App\Models\RpsMatch;
use App\Models\SvgMatch;
use App\Services\Rps\RpsMatchAnalysisService;
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
    public function show(RpsMatchAnalysisService $analysis, AiModel $aiModel): View
    {
        // Load RPS matches for this model
        $rpsMatches = RpsMatch::query()
            ->playedBy($aiModel)
            ->with(['player1', 'player2', 'winner'])
            ->latest()
            ->take(4)
            ->get();

        $totalRpsMatches = $aiModel->rpsMatches()->count();
        $totalRpsWins = $aiModel->rpsMatchesWon()->count();
        $rpsWinRate = $totalRpsMatches > 0 ? $totalRpsWins / $totalRpsMatches : 0;

        $moveBreakdown = $aiModel->rpsMoveBreakdown();
        $strategyAnalysis = $analysis->getStrategyAnalysis($aiModel, $moveBreakdown);

        // Get SVG matches and stats
        $svgMatches = SvgMatch::query()
            ->playedBy($aiModel)
            ->with(['player1', 'player2', 'winner'])
            ->latest()
            ->take(2)
            ->get();

        $totalSvgMatches = $aiModel->svgMatchesAsPlayer1()->count() + $aiModel->svgMatchesAsPlayer2()->count();
        $totalSvgWins = $aiModel->svgMatchesWon()->count();
        $svgWinRate = $totalSvgMatches > 0 ? $totalSvgWins / $totalSvgMatches : 0;

        // Get best SVG artworks
        $bestArtworks = $aiModel->svgMatchesWon()
            ->latest()
            ->take(4)
            ->get()
            ->map(fn ($match) => [
                'match' => $match,
                'prompt' => $match->prompt,
                'svg_url' => $match->getWinnerSvgUrl(),
            ]);

        return view('models.show', [
            'model' => $aiModel,
            'rpsMatches' => $rpsMatches,
            'totalRpsMatches' => $totalRpsMatches,
            'totalRpsWins' => $totalRpsWins,
            'rpsWinRate' => $rpsWinRate,
            'moveBreakdown' => $moveBreakdown,
            'strategyAnalysis' => $strategyAnalysis,
            'svgMatches' => $svgMatches,
            'totalSvgMatches' => $totalSvgMatches,
            'totalSvgWins' => $totalSvgWins,
            'svgWinRate' => $svgWinRate,
            'bestArtworks' => $bestArtworks,
            'activeTab' => 'overview',
        ]);
    }
}
