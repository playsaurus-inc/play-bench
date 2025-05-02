<?php

namespace App\Http\Controllers\AiModels;

use App\Http\Controllers\Controller;
use App\Models\AiModel;
use App\Models\SvgMatch;
use Illuminate\View\View;

class SvgController extends Controller
{
    /**
     * Display the SVG Drawing-specific performance for this model.
     */
    public function show(AiModel $aiModel): View
    {
        // Get SVG matches
        $svgMatches = SvgMatch::query()
            ->playedBy($aiModel)
            ->with(['player1', 'player2', 'winner'])
            ->latest()
            ->take(10)
            ->get();

        // Calculate performance statistics
        $totalSvgMatches = $aiModel->svgMatchesAsPlayer1()->count() + $aiModel->svgMatchesAsPlayer2()->count();
        $totalSvgWins = $aiModel->svgMatchesWon()->count();
        $winRate = $totalSvgMatches > 0 ? $totalSvgWins / $totalSvgMatches : 0;

        // Get winning artwork samples
        $winningArtworks = $aiModel->svgMatchesWon()
            ->latest()
            ->take(9)
            ->get()
            ->map(function ($match) use ($aiModel) {
                return [
                    'match' => $match,
                    'prompt' => $match->prompt,
                    'svg_url' => $match->winner_id === $match->player1_id
                        ? $match->getPlayer1SvgUrl()
                        : $match->getPlayer2SvgUrl(),
                ];
            });

        // Get failed artwork samples (matches where this model lost)
        $failedArtworks = $aiModel->svgMatchesLost()
            ->with(['winner'])
            ->latest()
            ->take(6)
            ->get()
            ->map(function ($match) use ($aiModel) {
                return [
                    'match' => $match,
                    'prompt' => $match->prompt,
                    'svg_url' => $match->player1_id === $aiModel->id
                        ? $match->getPlayer1SvgUrl()
                        : $match->getPlayer2SvgUrl(),
                    'winner_url' => $match->winner_id === $match->player1_id
                        ? $match->getPlayer1SvgUrl()
                        : $match->getPlayer2SvgUrl(),
                    'winner_name' => $match->winner->name,
                ];
            });

        // Get opponents info
        $opponents = $this->getOpponents($aiModel);

        return view('models.show-svg', [
            'model' => $aiModel,
            'svgMatches' => $svgMatches,
            'winRate' => $winRate,
            'totalSvgMatches' => $totalSvgMatches,
            'totalSvgWins' => $totalSvgWins,
            'opponents' => $opponents,
            'winningArtworks' => $winningArtworks,
            'failedArtworks' => $failedArtworks,
            'activeTab' => 'svg',
        ]);
    }

    /**
     * Get all opponents of the given AI model and their win rates.
     */
    protected function getOpponents(AiModel $aiModel)
    {
        return AiModel::query()
            ->whereHas('svgMatchesAsPlayer1', fn ($q) => $q->where('player2_id', $aiModel->id))
            ->orWhereHas('svgMatchesAsPlayer2', fn ($q) => $q->where('player1_id', $aiModel->id))
            ->with([
                'svgMatchesAsPlayer1' => fn ($q) => $q->where('player2_id', $aiModel->id),
                'svgMatchesAsPlayer2' => fn ($q) => $q->where('player1_id', $aiModel->id),
            ])
            ->get()
            ->map(function (AiModel $opponent) use ($aiModel) {
                $matches = $opponent->svgMatchesAsPlayer1->concat($opponent->svgMatchesAsPlayer2);
                $wins = $matches->where('winner_id', $aiModel->id)->count();
                $total = $matches->count();

                return (object) [
                    'model' => $opponent,
                    'win_rate' => $total > 0 ? $wins / $total : 0,
                    'total_matches' => $total,
                ];
            });
    }
}
