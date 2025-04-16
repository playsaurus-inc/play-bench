<?php

namespace App\Models;

use App\Services\RpsMatchAnalysisService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Illuminate\Support\HtmlString;

class RpsMatch extends Model
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
            if (! $model->winner_id) {
                $model->winner_id = static::determineWinner(
                    $model->player1, $model->player1_score,
                    $model->player2, $model->player2_score,
                )?->id;
            }

            if (! $model->player1_win_streak) {
                $model->player1_win_streak = static::calculateWinStreak($model->move_history, player: 1);
            }

            if (! $model->player2_win_streak) {
                $model->player2_win_streak = static::calculateWinStreak($model->move_history, player: 2);
            }

            if (! $model->rounds_played) {
                $model->rounds_played = Str::of($model->move_history)->explode(' ')->count() + 1;
            }

            if (! $model->player1_move_distribution) {
                $model->player1_move_distribution = static::calculateMoveDistribution($model->move_history, player: 1);
            }

            if (! $model->player2_move_distribution) {
                $model->player2_move_distribution = static::calculateMoveDistribution($model->move_history, player: 2);
            }
        });
    }

    /**
     * Determine the winner of the match based on scores.
     */
    public static function determineWinner(
        AiModel|int $player1,
        int $player1Score,
        AiModel|int $player2,
        int $player2Score
    ): AiModel|int|null {
        if ($player1Score > $player2Score) {
            return $player1;
        } elseif ($player2Score > $player1Score) {
            return $player2;
        }

        return null; // Tie
    }

    /**
     * Determine the result of a round based on the two moves
     *
     * @param  string  $p1Move  Player 1's move (r, p, or s)
     * @param  string  $p2Move  Player 2's move (r, p, or s)
     * @return string Result code (1, 2, or t)
     */
    public static function determineRoundResult(string $p1Move, string $p2Move): string
    {
        if ($p1Move === $p2Move) {
            return 't';
        }

        if (
            ($p1Move === 'r' && $p2Move === 's') ||
            ($p1Move === 'p' && $p2Move === 'r') ||
            ($p1Move === 's' && $p2Move === 'p')
        ) {
            return '1';
        }

        return '2';
    }

    /**
     * Determine the scores of the players based on the move history
     *
     * @param  string  $moveHistory  The move history string
     * @return [int, int] An array containing the scores of player 1 and player 2
     */
    public static function determineScoresFromMoveHistory(string $moveHistory): array
    {
        return [
            Str::substrCount($moveHistory, '1'),
            Str::substrCount($moveHistory, '2'),
        ];
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

        $query->where(fn ($q) =>
            $q->where('player1_id', $player)->orWhere('player2_id', $player)
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
     * @return int  The longest win streak for the given player
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
        return Attribute::get(fn() => $this->calculateMoveDistributionPercentages($this->player1_move_distribution));
    }

    /**
     * Get the move distribution percentages for player 2.
     */
    public function player2MoveDistributionPercentages(): Attribute
    {
        return Attribute::get(fn() => $this->calculateMoveDistributionPercentages($this->player2_move_distribution));
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
}
