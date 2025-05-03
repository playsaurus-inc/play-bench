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
    public function home(Request $request): View
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

        $models = AiModel::query()
            ->withCount([
                'rpsMatchesAsPlayer1',
                'rpsMatchesAsPlayer2',
                'rpsMatchesWon',
                'rpsMatchesLost',
                'rpsMatchesAsPlayer1 as rps_matches_as_player1_tied_count' => fn (Builder $query) => $query->whereNull('winner_id'),
                'rpsMatchesAsPlayer2 as rps_matches_as_player2_tied_count' => fn (Builder $query) => $query->whereNull('winner_id'),
            ])
            ->get()
            ->map(function ($model) {
                $model->total_rps_matches = $model->rps_matches_as_player1_count + $model->rps_matches_as_player2_count;
                $model->rps_matches_tied_count = $model->rps_matches_as_player1_tied_count + $model->rps_matches_as_player2_tied_count;
                $model->win_rate = $model->rps_matches_won_count / max(1, $model->total_rps_matches);

                return $model;
            })
            ->sortByDesc('rps_elo')
            ->reject(fn ($model) => $model->total_rps_matches < 1);

        return view('rps.index', [
            'totalMatchesCount' => RpsMatch::count(),
            'totalRoundsCount' => RpsMatch::sum('rounds_played'),
            'modelsCount' => AiModel::count(),
            'latestMatch' => $latestMatch,
            'closeMatch' => $closeMatch,
            'mostRoundsMatch' => $mostRoundsMatch,
            'models' => $models->values(),
            'topModels' => $models->where('total_rps_matches', '>=', 5)->take(3),
        ]);
    }

    /**
     * Display a paginated list of RPS matches with enhanced filtering options.
     */
    public function index(Request $request)
    {
        // Get all models for filtering options
        $models = AiModel::orderBy('name')->get();
        
        $query = RpsMatch::with(['player1', 'player2', 'winner'])
            ->when($request->model, function ($query, $modelId) {
                return $query->playedBy($modelId);
            })
            ->when($request->has('winner'), function ($query) use ($request) {
                if ($request->winner === 'tie') {
                    return $query->whereNull('winner_id');
                }
                return $query->where('winner_id', $request->winner);
            })
            ->when($request->has('match_type') && $request->match_type === 'close', function ($query) {
                return $query->whereRaw('ABS(player1_score - player2_score) <= 3')
                             ->where('rounds_played', '>=', 10);
            })
            ->when($request->sort, function ($query, $sort) {
                return match ($sort) {
                    'rounds' => $query->orderBy('rounds_played', 'desc'),
                    'score_diff' => $query->orderByRaw('ABS(player1_score - player2_score) DESC'),
                    'date_asc' => $query->orderBy('created_at', 'asc'),
                    default => $query->orderBy('created_at', 'desc'),
                };
            }, function ($query) {
                return $query->orderBy('created_at', 'desc');
            });

        $matches = $query->paginate(12)->withQueryString();
        
        // Get some stats for the header
        $stats = [
            'total' => RpsMatch::count(),
            'rounds' => RpsMatch::sum('rounds_played'),
            'ties' => RpsMatch::whereNull('winner_id')->count(),
        ];

        // If filtering by model, get the model for additional info
        $selectedModel = null;
        if ($request->model) {
            $selectedModel = AiModel::find($request->model);
        }

        return view('rps.matches.index', [
            'matches' => $matches,
            'stats' => $stats,
            'models' => $models,
            'selectedModel' => $selectedModel,
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