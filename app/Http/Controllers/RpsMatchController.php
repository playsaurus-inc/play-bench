<?php

namespace App\Http\Controllers;

use App\Models\AiModel;
use App\Models\RpsMatch;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Database\Eloquent\Builder;

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

        // Close matches (small difference in score)
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
        $similarMatches = RpsMatch::where(function (Builder $query) use ($rpsMatch) {
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

        // Calculate win streak patterns
        $rounds = $rpsMatch->getRounds();
        $streakData = $this->calculateStreakData($rounds);

        return view('rps.show', compact('rpsMatch', 'similarMatches', 'streakData'));
    }

    /**
     * Calculate streak data from rounds.
     *
     * @param array $rounds The rounds data
     * @return array Streak data for visualization
     */
    protected function calculateStreakData(array $rounds): array
    {
        if (empty($rounds)) {
            return [
                'player1_longest_streak' => 0,
                'player2_longest_streak' => 0,
                'player1_current_streak' => 0,
                'player2_current_streak' => 0,
                'streaks' => [],
            ];
        }

        $player1Streak = 0;
        $player2Streak = 0;
        $player1LongestStreak = 0;
        $player2LongestStreak = 0;
        $streaks = [];
        $currentStreak = ['player' => null, 'length' => 0, 'start' => 0];

        foreach ($rounds as $index => $round) {
            if ($round['result'] === 'player1_win') {
                $player1Streak++;
                $player2Streak = 0;

                if ($currentStreak['player'] !== 'player1') {
                    if ($currentStreak['player'] !== null && $currentStreak['length'] >= 3) {
                        $streaks[] = $currentStreak;
                    }
                    $currentStreak = ['player' => 'player1', 'length' => 1, 'start' => $index];
                } else {
                    $currentStreak['length']++;
                }

                $player1LongestStreak = max($player1LongestStreak, $player1Streak);
            } elseif ($round['result'] === 'player2_win') {
                $player2Streak++;
                $player1Streak = 0;

                if ($currentStreak['player'] !== 'player2') {
                    if ($currentStreak['player'] !== null && $currentStreak['length'] >= 3) {
                        $streaks[] = $currentStreak;
                    }
                    $currentStreak = ['player' => 'player2', 'length' => 1, 'start' => $index];
                } else {
                    $currentStreak['length']++;
                }

                $player2LongestStreak = max($player2LongestStreak, $player2Streak);
            } else {
                // It's a tie
                if ($currentStreak['player'] !== null && $currentStreak['length'] >= 3) {
                    $streaks[] = $currentStreak;
                }
                $currentStreak = ['player' => null, 'length' => 0, 'start' => 0];
                $player1Streak = 0;
                $player2Streak = 0;
            }
        }

        // Add the final streak if it's significant
        if ($currentStreak['player'] !== null && $currentStreak['length'] >= 3) {
            $streaks[] = $currentStreak;
        }

        return [
            'player1_longest_streak' => $player1LongestStreak,
            'player2_longest_streak' => $player2LongestStreak,
            'player1_current_streak' => $player1Streak,
            'player2_current_streak' => $player2Streak,
            'streaks' => $streaks,
        ];
    }
}
