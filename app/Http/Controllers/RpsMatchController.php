<?php

namespace App\Http\Controllers;

use App\Models\AiModel;
use App\Models\RpsMatch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RpsMatchController extends Controller
{
    /**
     * Display a listing of the RPS matches with filtering options.
     */
    public function index(Request $request): View
    {
        $latestMatch = RpsMatch::query()
            ->with(['player1', 'player2', 'winner'])
            ->latest()
            ->first();

        // Close match (small difference in score)
        $closeMatch = RpsMatch::with(['player1', 'player2', 'winner'])
            ->whereRaw('ABS(player1_score - player2_score) <= 3')
            ->where('rounds_played', '>=', 20)
            ->orderByRaw('ABS(player1_score - player2_score)')
            ->orderBy('rounds_played', 'desc')
            ->first();

        // Matches with the most rounds
        $mostRoundsMatch = RpsMatch::with(['player1', 'player2', 'winner'])
            ->orderBy('rounds_played', 'desc')
            ->first();

        return view('rps.index', [
            'totalMatchesCount' => RpsMatch::count(),
            'totalRoundsCount' => RpsMatch::sum('rounds_played'),
            'modelsCount' => AiModel::count(),
            'latestMatch' => $latestMatch,
            'closeMatch' => $closeMatch,
            'mostRoundsMatch' => $mostRoundsMatch,
        ]);
    }

    /**
     * Display the specified RPS match with enhanced visualization.
     */
    public function show(RpsMatch $rpsMatch): View
    {
        $rpsMatch->load(['player1', 'player2', 'winner']);

        // Get similar matches between the same models
        $similarMatches = RpsMatch::query()
            ->where(function (Builder $query) use ($rpsMatch) {
                $query->where(function (Builder $q) use ($rpsMatch) {
                    $q->where('player1_id', $rpsMatch->player1_id)
                        ->where('player2_id', $rpsMatch->player2_id);
                })->orWhere(function (Builder $q) use ($rpsMatch) {
                    $q->where('player1_id', $rpsMatch->player2_id)
                        ->where('player2_id', $rpsMatch->player1_id);
                });
            })
            ->where('id', '!=', $rpsMatch->id)
            ->with(['player1', 'player2', 'winner'])
            ->latest()
            ->limit(3)
            ->get();

        return view('rps.matches.show', [
            'rpsMatch' => $rpsMatch,
            'similarMatches' => $similarMatches,
        ]);
    }
}
