<?php

namespace App\Http\Controllers;

use App\Models\AiModel;
use App\Models\SvgMatch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class SvgMatchController extends Controller
{
    /**
     * Display the index page for SVG matches.
     */
    public function index(): View
    {
        // This will be implemented later, but we create the stub for completeness
        return view('svg.index');
    }

    /**
     * Display the specified SVG match with detailed visualization.
     */
    public function show(SvgMatch $svgMatch): View
    {
        $svgMatch->load(['player1', 'player2', 'winner']);

        // Get similar matches between the same models
        $similarMatches = SvgMatch::query()
            ->where(function (Builder $query) use ($svgMatch) {
                $query->where(function (Builder $q) use ($svgMatch) {
                    $q->where('player1_id', $svgMatch->player1_id)
                        ->where('player2_id', $svgMatch->player2_id);
                })->orWhere(function (Builder $q) use ($svgMatch) {
                    $q->where('player1_id', $svgMatch->player2_id)
                        ->where('player2_id', $svgMatch->player1_id);
                });
            })
            ->where('id', '!=', $svgMatch->id)
            ->with(['player1', 'player2', 'winner'])
            ->latest()
            ->take(3)
            ->get();

        // Get models by most SVG matches
        $topSvgCreators = AiModel::query()
            ->withCount(['svgMatchesWon'])
            ->orderByDesc('svg_matches_won_count')
            ->take(3)
            ->get();

        return view('svg.matches.show', [
            'svgMatch' => $svgMatch,
            'similarMatches' => $similarMatches,
            'topSvgCreators' => $topSvgCreators,
        ]);
    }
}
