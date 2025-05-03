<?php

namespace App\Http\Controllers;

use App\Models\AiModel;
use App\Models\SvgMatch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SvgMatchController extends Controller
{
    /**
     * Display the index page for SVG matches.
     */
    public function home(): View
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

        $topSvgCreators = $models
            ->sortByDesc('svg_matches_won_count')
            ->take(3)
            ->load(['svgMatchesWon' => fn ($q) => $q->take(12)])
            ->map(function ($model) {
                $model->svg_samples = $model->svgMatchesWon->map->getWinnerSvgUrl();
                return $model;
            })
            ->values();

        return view('svg.index', [
            'totalMatchesCount' => SvgMatch::count(),
            'giraffeCount' => SvgMatch::where('prompt', 'like', '%giraffe%')->count(),
            'modelsCount' => $models->count(),
            'latestMatch' => $latestMatch,
            'mostCreativeMatch' => $mostCreativeMatch ?? $latestMatch, // Fallback to latest if none found
            'visuallyInterestingMatch' => $visuallyInterestingMatch ?? $latestMatch, // Fallback to latest if none found
            'models' => $models->values(),
            'topCreators' => $topSvgCreators,
        ]);
    }

    /**
     * Display a paginated list of SVG matches with enhanced filtering options.
     */
    public function index(Request $request): View
    {
        // Get all models for filtering options
        $models = AiModel::orderBy('name')->get();

        $query = SvgMatch::with(['player1', 'player2', 'winner'])
            ->when($request->model, function ($query, $modelId) {
                return $query->playedBy($modelId);
            })
            ->when($request->contender, function ($query, $contenderId) use ($request) {
                return ($request->model)
                    ? $query->playedAgainst($request->model, $contenderId)
                    : $query->playedBy($contenderId);
            })
            ->when($request->has('winner'), function ($query) use ($request) {
                return $query->where('winner_id', $request->winner);
            })
            ->when($request->has('prompt'), function ($query) use ($request) {
                return $query->where('prompt', 'like', '%' . $request->prompt . '%');
            })
            ->when($request->sort, function ($query, $sort) {
                return match ($sort) {
                    'date_asc' => $query->orderBy('created_at', 'asc'),
                    'complexity' => $query->orderByRaw("CAST(COALESCE(json_extract(player1_features, '$.path_commands'), 0) AS INTEGER) +
                                               CAST(COALESCE(json_extract(player2_features, '$.path_commands'), 0) AS INTEGER) DESC"),
                    'animations' => $query->orderByRaw("CAST(COALESCE(json_extract(player1_features, '$.animations'), 0) AS INTEGER) +
                                               CAST(COALESCE(json_extract(player2_features, '$.animations'), 0) AS INTEGER) DESC"),
                    'text' => $query->orderByRaw("CAST(COALESCE(json_extract(player1_features, '$.text_elements'), 0) AS INTEGER) +
                                         CAST(COALESCE(json_extract(player2_features, '$.text_elements'), 0) AS INTEGER) DESC"),
                    'gradients' => $query->orderByRaw("CAST(COALESCE(json_extract(player1_features, '$.gradients'), 0) AS INTEGER) +
                                            CAST(COALESCE(json_extract(player2_features, '$.gradients'), 0) AS INTEGER) DESC"),
                    default => $query->orderBy('created_at', 'desc'),
                };
            }, function ($query) {
                return $query->orderBy('created_at', 'desc');
            });

        $matches = $query->paginate(12)->withQueryString();

        // Get stats for the header
        $stats = [
            'total' => SvgMatch::count(),
            'models' => AiModel::whereHas('svgMatchesAsPlayer1')->orWhereHas('svgMatchesAsPlayer2')->count(),
            'unique_prompts' => SvgMatch::distinct('prompt')->count(),
        ];

        $selectedModel = $request->model ? AiModel::from($request->model) : null;
        $selectedContender = $request->contender ? AiModel::from($request->contender) : null;

        return view('svg.matches.index', [
            'matches' => $matches,
            'stats' => $stats,
            'models' => $models,
            'selectedModel' => $selectedModel,
            'selectedContender' => $selectedContender,
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

        // Get SVG comparative features
        $svgFeatures = collect($svgMatch->getComparativeSvgFeatures())
            ->reject(fn ($feat, $key) => in_array($key, ['width', 'height']))
            ->groupBy('category');

        return view('svg.matches.show', [
            'svgMatch' => $svgMatch,
            'similarMatches' => $similarMatches,
            'topSvgCreators' => $topSvgCreators,
            'svgFeatures' => $svgFeatures,
        ]);
    }
}
