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
        // Latest SVG match
        $latestMatch = SvgMatch::query()
            ->with(['player1', 'player2', 'winner'])
            ->latest()
            ->first();

        // Most creative (Judge reasoning contains "creative" or "creativity")
        $mostCreativeMatch = SvgMatch::query()
            ->with(['player1', 'player2', 'winner'])
            ->where('judge_reasoning', 'like', '%creativ%')
            ->inRandomOrder()
            ->first();

        // Match with interesting visual elements (adjust search terms as needed)
        $visuallyInterestingMatch = SvgMatch::query()
            ->with(['player1', 'player2', 'winner'])
            ->where(function (Builder $query) {
                $query->where('judge_reasoning', 'like', '%visual%')
                    ->orWhere('judge_reasoning', 'like', '%color%')
                    ->orWhere('judge_reasoning', 'like', '%composition%')
                    ->orWhere('judge_reasoning', 'like', '%aesthetic%');
            })
            ->inRandomOrder()
            ->first();

        // Get models with SVG performance stats
        $models = AiModel::query()
            ->withCount([
                'svgMatchesAsPlayer1',
                'svgMatchesAsPlayer2',
                'svgMatchesWon',
                'svgMatchesLost',
                'svgMatchesAsPlayer1 as svg_matches_as_player1_tied_count' => fn (Builder $query) => $query->whereNull('winner_id'),
                'svgMatchesAsPlayer2 as svg_matches_as_player2_tied_count' => fn (Builder $query) => $query->whereNull('winner_id'),
            ])
            ->get()
            ->map(function ($model) {
                $model->total_svg_matches = $model->svg_matches_as_player1_count + $model->svg_matches_as_player2_count;
                $model->win_rate = $model->total_svg_matches > 0
                    ? $model->svg_matches_won_count / $model->total_svg_matches
                    : 0;

                return $model;
            })
            ->sortByDesc('svg_elo')
            ->reject(fn ($model) => $model->total_svg_matches < 1);

        return view('svg.index', [
            'totalMatchesCount' => SvgMatch::count(),
            'giraffeCount' => SvgMatch::where('prompt', 'like', '%giraffe%')->count(),
            'modelsCount' => $models->count(),
            'latestMatch' => $latestMatch,
            'mostCreativeMatch' => $mostCreativeMatch ?? $latestMatch, // Fallback to latest if none found
            'visuallyInterestingMatch' => $visuallyInterestingMatch ?? $latestMatch, // Fallback to latest if none found
            'models' => $models->values(),
            'topCreators' => $models->sortByDesc('svg_matches_won_count')->take(3),
        ]);
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
            ->take(6)
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
