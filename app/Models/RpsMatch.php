<?php

namespace App\Models;

use App\Models\Contracts\RankedMatch;
use App\Services\Rps\RpsMatchAnalysisService;
use App\Services\Rps\RpsMove;
use App\Services\Rps\RpsRound;
use App\Services\Rps\RpsRoundResult;
use App\Support\Statistics;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class RpsMatch extends Model implements RankedMatch
{
    /** @use HasFactory<\Database\Factories\RpsMatchFactory> */
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'is_forced_completion' => 'boolean',
        'move_history' => 'string',
        'rounds_played' => 'integer',
        'player1_score' => 'integer',
        'player2_score' => 'integer',
        'player1_win_streak' => 'integer',
        'player2_win_streak' => 'integer',
        'player1_move_distribution' => 'array',
        'player2_move_distribution' => 'array',
    ];

    /**
     * Perform any actions required before the model boots.
     *
     * @return void
     */
    protected static function booting()
    {
        static::saving(function (self $model) {
            if (! isset($model->player1_score)) {
                $model->player1_score = Str::substrCount($model->move_history, '1');
            }

            if (! isset($model->player2_score)) {
                $model->player2_score = Str::substrCount($model->move_history, '2');
            }

            if (! isset($model->winner_id)) {
                $model->winner_id = static::determineWinner(
                    $model->player1, $model->player1_score,
                    $model->player2, $model->player2_score,
                )?->id;
            }

            if (! isset($model->player1_win_streak)) {
                $model->player1_win_streak = static::calculateWinStreak($model->move_history, player: 1);
            }

            if (! isset($model->player2_win_streak)) {
                $model->player2_win_streak = static::calculateWinStreak($model->move_history, player: 2);
            }

            if (! isset($model->rounds_played)) {
                $model->rounds_played = Str::of($model->move_history)->explode(' ')->count() + 1;
            }

            if (! isset($model->player1_move_distribution)) {
                $model->player1_move_distribution = static::calculateMoveDistribution($model->move_history, player: 1);
            }

            if (! isset($model->player2_move_distribution)) {
                $model->player2_move_distribution = static::calculateMoveDistribution($model->move_history, player: 2);
            }
        });
    }

    /**
     * Recompute the match statistics.
     */
    public function recompute(): void
    {
        // Just null the values so they can be recomputed again in the `saving` event
        $this->update([
            'winner_id' => null,
            'player1_win_streak' => null,
            'player2_win_streak' => null,
            'rounds_played' => null,
            'player1_move_distribution' => null,
            'player2_move_distribution' => null,
        ]);
    }

    /**
     * Determine the winner of the match based on scores.
     */
    public static function determineWinner(
        AiModel|int $player1,
        int $player1Score,
        AiModel|int $player2,
        int $player2Score,
    ): AiModel|int|null {
        // If scores are identical, it's a definite tie
        if ($player1Score === $player2Score) {
            return null; // Tie
        }

        // Check if the difference is statistically significant
        if (! Statistics::isScoreDifferenceSignificant($player1Score, $player2Score)) {
            return null; // Statistical tie - difference not significant
        }

        // Return the model with the higher score
        return $player1Score > $player2Score ? $player1 : $player2;
    }

    /**
     * Check if this match is a statistical tie (different scores but not statistically significant).
     */
    public function isStatisticalTie(): bool
    {
        if (! $this->isTie()) {
            return false;
        }

        // The match is a tie, but scores are different
        return $this->player1_score !== $this->player2_score;
    }

    /**
     * Get the statistical significance threshold for this match.
     */
    public function getDifferenceThreshold(): float
    {
        return Statistics::getDifferenceThreshold($this->player1_score, $this->player2_score);
    }

    /**
     * Get the AI model that played as player 1
     */
    public function player1(): BelongsTo
    {
        return $this->belongsTo(AiModel::class, 'player1_id');
    }

    /**
     * Get the AI model that played as player 2
     */
    public function player2(): BelongsTo
    {
        return $this->belongsTo(AiModel::class, 'player2_id');
    }

    /**
     * Get the AI model that won the match
     */
    public function winner(): BelongsTo
    {
        return $this->belongsTo(AiModel::class, 'winner_id');
    }

    /**
     * Scopes the query to only include matches with a specific player.
     */
    public function scopePlayedBy(Builder $query, AiModel|int $player): void
    {
        $player = $player instanceof AiModel ? $player->id : $player;

        $query->where(fn ($q) => $q->where('player1_id', $player)->orWhere('player2_id', $player)
        );
    }

    /**
     * Scopes the query to only include matches with a specific winner.
     */
    public function scopeWonBy(Builder $query, AiModel|int $winner): void
    {
        $winner = $winner instanceof AiModel ? $winner->id : $winner;

        $query->where('winner_id', $winner);
    }

    /**
     * Check if the match ended in a tie
     */
    public function isTie(): bool
    {
        return $this->winner_id === null;
    }

    /**
     * Get an array of rounds from the match history
     *
     * @return array<int, array{player1_move: string, player2_move: string, result: string}>
     */
    public function getRounds(): array
    {
        $rounds = [];
        $moveHistoryItems = explode(' ', trim($this->move_history));

        foreach ($moveHistoryItems as $index => $item) {
            if (strlen($item) >= 3) {
                $rounds[] = [
                    'player1_move' => $this->expandMove($item[0]),
                    'player2_move' => $this->expandMove($item[1]),
                    'result' => $this->expandResult($item[2]),
                    'round_number' => $index + 1,
                ];
            }
        }

        return $rounds;
    }

    /**
     * Convert a move abbreviation to its full name
     */
    protected function expandMove(string $move): string
    {
        return match ($move) {
            'r' => 'rock',
            'p' => 'paper',
            's' => 'scissors',
            default => 'unknown'
        };
    }

    /**
     * Convert a result abbreviation to its full description
     */
    protected function expandResult(string $result): string
    {
        return match ($result) {
            '1' => 'player1_win',
            '2' => 'player2_win',
            't' => 'tie',
            default => 'unknown'
        };
    }

    /**
     * Get the number of decisive rounds played
     */
    public function getDecisiveRounds(): int
    {
        return $this->player1_score + $this->player2_score;
    }

    /**
     * Get the duration of the match in seconds
     */
    public function getDuration(): ?int
    {
        if (! $this->started_at || ! $this->ended_at) {
            return null;
        }

        return $this->ended_at->diffInSeconds($this->started_at);
    }

    /**
     * Get the win rate for player 1
     */
    public function getPlayer1WinRate(): float
    {
        if ($this->rounds_played === 0) {
            return 0;
        }

        return $this->player1_score / $this->rounds_played;
    }

    /**
     * Get the win rate for player 2
     */
    public function getPlayer2WinRate(): float
    {
        if ($this->rounds_played === 0) {
            return 0;
        }

        return $this->player2_score / $this->rounds_played;
    }

    /**
     * Get the tie rate
     */
    public function getTieRate(): float
    {
        if ($this->rounds_played === 0) {
            return 0;
        }

        $ties = $this->rounds_played - $this->player1_score - $this->player2_score;

        return $ties / $this->rounds_played;
    }

    /**
     * Calculate the win streak for the given player based on the move history.
     *
     * @param  string  $moveHistory  The move history string
     * @param  int  $player  The player number
     * @return int The longest win streak for the given player
     */
    public static function calculateWinStreak(string $moveHistory, int $player): int
    {
        $wins = Str::of($moveHistory)->explode(' ')->map(fn ($s) => $s[2]);

        $streak = 0;
        $maxStreak = 0;
        foreach ($wins as $result) {
            if ($result === (string) $player) {
                $streak++;
                $maxStreak = max($maxStreak, $streak);
            } else {
                $streak = 0;
            }
        }

        return $maxStreak;
    }

    /**
     * Calculate the move distribution for the given player based on the move history.
     *
     * @param  string  $moveHistory  The move history string
     * @param  int  $player  The player number
     * @return array<{rock: int, paper: int, scissors: int}>  The move distribution for the given player
     */
    public static function calculateMoveDistribution(string $moveHistory, int $player): array
    {
        $index = $player === 1 ? 0 : 1;
        $moves = Str::of($moveHistory)->explode(' ')->map(fn ($s) => $s[$index]);

        return [
            'rock' => $moves->filter(fn ($m) => $m === 'r')->count(),
            'paper' => $moves->filter(fn ($m) => $m === 'p')->count(),
            'scissors' => $moves->filter(fn ($m) => $m === 's')->count(),
        ];
    }

    /**
     * Get the move distribution percentages for player 1.
     */
    public function player1MoveDistributionPercentages(): Attribute
    {
        return Attribute::get(fn () => $this->calculateMoveDistributionPercentages($this->player1_move_distribution));
    }

    /**
     * Get the move distribution percentages for player 2.
     */
    public function player2MoveDistributionPercentages(): Attribute
    {
        return Attribute::get(fn () => $this->calculateMoveDistributionPercentages($this->player2_move_distribution));
    }

    /**
     * Calculate the move distribution percentages for the given player.
     *
     * @param  array<{rock: int, paper: int, scissors: int}>  $moveDistribution  The move distribution for the given player
     * @return array<{rock: float, paper: float, scissors: float}>  The move distribution percentages for the given player
     */
    protected function calculateMoveDistributionPercentages(array $moveDistribution): array
    {
        $totalMoves = array_sum($moveDistribution);

        if ($totalMoves === 0) {
            return [
                'rock' => 0,
                'paper' => 0,
                'scissors' => 0,
            ];
        }

        return [
            'rock' => $moveDistribution['rock'] / $totalMoves,
            'paper' => $moveDistribution['paper'] / $totalMoves,
            'scissors' => $moveDistribution['scissors'] / $totalMoves,
        ];
    }

    /**
     * Get strategic insights text for the match
     */
    public function getStrategicInsights(): HtmlString
    {
        return RpsMatchAnalysisService::strategicInsights($this);
    }

    /**
     * Gets the player 1 of the match.
     */
    public function getPlayer1(): AiModel
    {
        return $this->player1;
    }

    /**
     * Gets the player 2 of the match.
     */
    public function getPlayer2(): AiModel
    {
        return $this->player2;
    }

    /**
     * Gets the outcome of the match. '1' for player 1 win, '2' for player 2 win, 't' for tie.
     */
    public function getOutcome(): string
    {
        return match ($this->winner_id) {
            $this->player1_id => '1',
            $this->player2_id => '2',
            default => 't',
        };
    }

    /**
     * Updates the ELO ratings of the match.
     */
    public function updateEloRatings(
        float $player1EloBefore,
        float $player2EloBefore,
        float $player1EloAfter,
        float $player2EloAfter
    ): void {
        $this->update([
            'player1_elo_before' => $player1EloBefore,
            'player2_elo_before' => $player2EloBefore,
            'player1_elo_after' => $player1EloAfter,
            'player2_elo_after' => $player2EloAfter,
        ]);
    }

    /**
     * Generate cumulative win data for charting
     *
     * @return array{labels: array<int>, player1Data: array<int>, player2Data: array<int>, player1Name: string, player2Name: string}
     */
    public function getCumulativeWinChartData(): array
    {
        $rounds = $this->getRounds();
        $labels = [];
        $player1CumulativeWins = [];
        $player2CumulativeWins = [];

        $player1Wins = 0;
        $player2Wins = 0;

        foreach ($rounds as $index => $round) {
            $roundNumber = $round['round_number'];
            $labels[] = $roundNumber;

            if ($round['result'] === 'player1_win') {
                $player1Wins++;
            } elseif ($round['result'] === 'player2_win') {
                $player2Wins++;
            }

            $player1CumulativeWins[] = $player1Wins;
            $player2CumulativeWins[] = $player2Wins;
        }

        return [
            'labels' => $labels,
            'player1Data' => $player1CumulativeWins,
            'player2Data' => $player2CumulativeWins,
            'player1Name' => $this->player1->name,
            'player2Name' => $this->player2->name,
        ];
    }

    /**
     * Generate win percentage over time data for charting
     *
     * @return array{labels: array<int>, player1Data: array<float>, player2Data: array<float>, player1Name: string, player2Name: string}
     */
    public function getWinPercentageChartData(): array
    {
        $rounds = $this->getRounds();
        $labels = [];
        $player1WinPercentages = [];
        $player2WinPercentages = [];

        $player1Wins = 0;
        $player2Wins = 0;

        foreach ($rounds as $index => $round) {
            $roundNumber = $round['round_number'];
            $labels[] = $roundNumber;

            if ($round['result'] === 'player1_win') {
                $player1Wins++;
            } elseif ($round['result'] === 'player2_win') {
                $player2Wins++;
            }

            // Calculate percentages based only on decisive rounds (excluding ties)
            $totalDecisiveRounds = $player1Wins + $player2Wins;
            if ($totalDecisiveRounds > 0) {
                $player1WinPercentages[] = ($player1Wins / $totalDecisiveRounds) * 100;
                $player2WinPercentages[] = ($player2Wins / $totalDecisiveRounds) * 100;
            } else {
                // If no decisive rounds yet, use 50-50 split
                $player1WinPercentages[] = 50;
                $player2WinPercentages[] = 50;
            }
        }

        return [
            'labels' => $labels,
            'player1Data' => $player1WinPercentages,
            'player2Data' => $player2WinPercentages,
            'player1Name' => $this->player1->name,
            'player2Name' => $this->player2->name,
        ];
    }
}
