<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

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
        // Move history is `abc abc abc` where `a` is player 1's move, `b` is player 2's move, and `c` is the result
        // Results can be: `1` (player 1 wins), `2` (player 2 wins), or `t` (tie)
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
}
