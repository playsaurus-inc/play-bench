<?php

namespace App\Http\Controllers;

use App\Models\AiModel;
use App\Models\RpsMatch;
use App\Models\ChessMatch;
use App\Models\SvgMatch;
use Illuminate\Http\Request;
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

    /**
     * Display the RPS-specific performance for this model.
     */
    public function showRps(AiModel $aiModel): View
    {
        // Get RPS matches
        $rpsMatches = RpsMatch::query()
            ->playedBy($aiModel)
            ->with(['player1', 'player2', 'winner'])
            ->latest()
            ->take(10)
            ->get();

        // Calculate performance statistics
        $totalRpsMatches = $aiModel->rpsMatches()->count();
        $totalRpsWins = $aiModel->rpsMatchesWon()->count();
        $winRate = $totalRpsMatches > 0 ? $totalRpsWins / $totalRpsMatches : 0;

        // Calculate move breakdown
        $moveBreakdown = $this->calculateRpsMoveBreakdown($aiModel);

        // Get most impressive victory
        $mostImpressiveVictory = $this->getMostImpressiveRpsVictory($aiModel);

        // Get opponents info
        $opponents = $this->getRpsOpponents($aiModel);

        return view('models.show-rps', [
            'model' => $aiModel,
            'rpsMatches' => $rpsMatches,
            'winRate' => $winRate,
            'totalRpsMatches' => $totalRpsMatches,
            'totalRpsWins' => $totalRpsWins,
            'opponents' => $opponents,
            'moveBreakdown' => $moveBreakdown,
            'mostImpressiveVictory' => $mostImpressiveVictory,
            'strategyAnalysis' => $this->getRpsStrategyAnalysis($aiModel, $moveBreakdown),
            'activeTab' => 'rps',
        ]);
    }

    /**
     * Display the Chess-specific performance for this model.
     * (Placeholder for future implementation)
     */
    public function showChess(AiModel $aiModel): View
    {
        return view('models.show-chess', [
            'model' => $aiModel,
            'activeTab' => 'chess',
        ]);
    }

    /**
     * Display the SVG Drawing-specific performance for this model.
     * (Placeholder for future implementation)
     */
    public function showSvg(AiModel $aiModel): View
    {
        return view('models.show-svg', [
            'model' => $aiModel,
            'activeTab' => 'svg',
        ]);
    }

    /**
     * Calculate the move breakdown of the given AI model for Rock Paper Scissors.
     */
    protected function calculateRpsMoveBreakdown(AiModel $aiModel): array
    {
        return [
            'rock' => $aiModel->rpsMatchesAsPlayer1()->sum('player1_move_distribution->rock') +
                $aiModel->rpsMatchesAsPlayer2()->sum('player2_move_distribution->rock'),
            'paper' => $aiModel->rpsMatchesAsPlayer1()->sum('player1_move_distribution->paper') +
                $aiModel->rpsMatchesAsPlayer2()->sum('player2_move_distribution->paper'),
            'scissors' => $aiModel->rpsMatchesAsPlayer1()->sum('player1_move_distribution->scissors') +
                $aiModel->rpsMatchesAsPlayer2()->sum('player2_move_distribution->scissors'),
        ];
    }

    /**
     * Get all RPS opponents of the given AI model and their win rates.
     */
    protected function getRpsOpponents(AiModel $aiModel)
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

                return (object) [
                    'model' => $opponent,
                    'win_rate' => $total > 0 ? $wins / $total : 0,
                    'total_matches' => $total,
                ];
            });
    }

    /**
     * Get the most impressive RPS victory of the given AI model.
     */
    protected function getMostImpressiveRpsVictory(AiModel $aiModel): ?RpsMatch
    {
        return RpsMatch::query()
            ->wonBy($aiModel)
            ->with(['player1', 'player2'])
            ->orderByRaw('ABS(player1_score - player2_score) DESC')
            ->first();
    }

    /**
     * Analyze the RPS strategy of the given AI model based on move breakdown.
     */
    protected function getRpsStrategyAnalysis(AiModel $aiModel, array $moveBreakdown): string
    {
        $totalMoves = $moveBreakdown['rock'] + $moveBreakdown['paper'] + $moveBreakdown['scissors'];
        if ($totalMoves === 0) {
            return 'No moves recorded for this AI model.';
        }

        $highestMove = array_search(max($moveBreakdown), $moveBreakdown);

        $perfectDistribution = abs(($moveBreakdown['rock'] - $totalMoves / 3) / $totalMoves) < 0.1 &&
                            abs(($moveBreakdown['paper'] - $totalMoves / 3) / $totalMoves) < 0.1 &&
                            abs(($moveBreakdown['scissors'] - $totalMoves / 3) / $totalMoves) < 0.1;

        $name = ucfirst($aiModel->name);

        if ($perfectDistribution) {
            return "{$name} uses a highly balanced strategy, playing rock, paper, and scissors with nearly equal frequency. ".
                   'This makes its moves very difficult to predict, as there is no clear pattern to exploit.';
        } else {
            return "{$name} shows a preference for {$highestMove}, using it more frequently than other moves. ".
                   'This tendency could potentially be exploited by opponents who can detect and adapt to this pattern.';
        }
    }
}
