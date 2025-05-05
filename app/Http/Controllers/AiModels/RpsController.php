<?php

namespace App\Http\Controllers\AiModels;

use App\Http\Controllers\Controller;
use App\Models\AiModel;
use App\Models\RpsMatch;
use App\Services\Rps\RpsMatchAnalysisService;
use Illuminate\View\View;

class RpsController extends Controller
{
    /**
     * Display the RPS-specific performance for this model.
     */
    public function show(RpsMatchAnalysisService $analysis, AiModel $aiModel): View
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

        $moveBreakdown = $aiModel->rpsMoveBreakdown();

        $mostImpressiveVictory = $this->getMostImpressiveVictory($aiModel);

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
            'strategyAnalysis' => $analysis->getStrategyAnalysis($aiModel, $moveBreakdown),
            'activeTab' => 'rps',
        ]);
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
}
