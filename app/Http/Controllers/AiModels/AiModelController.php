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

        // Calculate RPS stats
        $totalRpsMatches = $aiModel->rpsMatches()->count();
        $totalRpsWins = $aiModel->rpsMatchesWon()->count();
        $rpsWinRate = $totalRpsMatches > 0 ? $totalRpsWins / $totalRpsMatches : 0;

        // Get RPS move distribution
        $moveBreakdown = $this->getRpsMoveBreakdown($aiModel);
        $strategyAnalysis = $analysis->getStrategyAnalysis($aiModel, $moveBreakdown);

        // Get top RPS opponents
        $rpsOpponents = $this->getRpsOpponents($aiModel, 3);

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
            'rpsOpponents' => $rpsOpponents,
            'svgMatches' => $svgMatches,
            'totalSvgMatches' => $totalSvgMatches,
            'totalSvgWins' => $totalSvgWins,
            'svgWinRate' => $svgWinRate,
            'bestArtworks' => $bestArtworks,
            'activeTab' => 'overview',
        ]);
    }

    /**
     * Get move distribution statistics for an AI model in RPS matches.
     */
    protected function getRpsMoveBreakdown(AiModel $aiModel): array
    {
        $matches = RpsMatch::query()->playedBy($aiModel)->get();
        $rockCount = 0;
        $paperCount = 0;
        $scissorsCount = 0;

        foreach ($matches as $match) {
            $isPlayer1 = $match->player1_id === $aiModel->id;
            $distribution = $isPlayer1 ? $match->player1_move_distribution : $match->player2_move_distribution;

            if (!$distribution) {
                continue;
            }

            $rockCount += $distribution['rock'] ?? 0;
            $paperCount += $distribution['paper'] ?? 0;
            $scissorsCount += $distribution['scissors'] ?? 0;
        }

        return [
            'rock' => $rockCount,
            'paper' => $paperCount,
            'scissors' => $scissorsCount,
        ];
    }

    /**
     * Get top opponents for RPS.
     */
    protected function getRpsOpponents(AiModel $aiModel, int $limit = 3)
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
                $matches1 = $opponent->rpsMatchesAsPlayer1->filter(
                    fn ($match) => $match->player2_id === $aiModel->id
                );
                $matches2 = $opponent->rpsMatchesAsPlayer2->filter(
                    fn ($match) => $match->player1_id === $aiModel->id
                );

                $matches = $matches1->concat($matches2);
                $wins = $matches->filter(fn ($match) => $match->winner_id === $aiModel->id)->count();
                $total = $matches->count();

                return (object) [
                    'model' => $opponent,
                    'win_rate' => $total > 0 ? $wins / $total : 0,
                    'total_matches' => $total,
                ];
            })
            ->sortByDesc('total_matches')
            ->take($limit);
    }
}
