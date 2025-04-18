<?php

namespace App\Http\Controllers\Models;

use App\Http\Controllers\Controller;
use App\Models\AiModel;
use App\Models\RpsMatch;
use Illuminate\View\View;

class RpsController extends Controller
{
    /**
     * Display the RPS-specific performance for this model.
     */
    public function show(AiModel $aiModel): View
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
        $moveBreakdown = $this->calculateMoveBreakdown($aiModel);

        // Get most impressive victory
        $mostImpressiveVictory = $this->getMostImpressiveVictory($aiModel);

        // Get opponents info
        $opponents = $this->getOpponents($aiModel);

        return view('models.show-rps', [
            'model' => $aiModel,
            'rpsMatches' => $rpsMatches,
            'winRate' => $winRate,
            'totalRpsMatches' => $totalRpsMatches,
            'totalRpsWins' => $totalRpsWins,
            'opponents' => $opponents,
            'moveBreakdown' => $moveBreakdown,
            'mostImpressiveVictory' => $mostImpressiveVictory,
            'strategyAnalysis' => $this->getStrategyAnalysis($aiModel, $moveBreakdown),
            'activeTab' => 'rps',
        ]);
    }

    /**
     * Calculate the move breakdown of the given AI model.
     */
    protected function calculateMoveBreakdown(AiModel $aiModel): array
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
     * Get all opponents of the given AI model and their win rates.
     */
    protected function getOpponents(AiModel $aiModel)
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
     * Get the most impressive victory of the given AI model.
     */
    protected function getMostImpressiveVictory(AiModel $aiModel): ?RpsMatch
    {
        return RpsMatch::query()
            ->wonBy($aiModel)
            ->with(['player1', 'player2'])
            ->orderByRaw('ABS(player1_score - player2_score) DESC')
            ->first();
    }

    /**
     * Analyze the strategy of the given AI model based on move breakdown.
     */
    protected function getStrategyAnalysis(AiModel $aiModel, array $moveBreakdown): string
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
