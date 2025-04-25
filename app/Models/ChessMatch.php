<?php

namespace App\Models;

use App\Models\Contracts\RankedMatch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChessMatch extends Model implements RankedMatch
{
    /** @use HasFactory<\Database\Factories\ChessMatchFactory> */
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
        'illegal_moves_white' => 'integer',
        'illegal_moves_black' => 'integer',
        'ply_count' => 'integer',
    ];

    /**
     * Perform any actions required before the model boots.
     */
    protected static function booting()
    {
        static::saving(function (self $model) {
            if (! $model->winner_id) {
                $model->winner_id = static::determineWinner(
                    $model->white, $model->black, $model->result
                )?->id;
            }

            if (! isset($model->loser_id)) {
                $model->loser_id = match ($model->winner_id) {
                    $model->white_id => $model->black_id,
                    $model->black_id => $model->white_id,
                    default => null, // Draw
                };
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
            'loser_id' => null,
        ]);
    }

    /**
     * Determine the winner of the match based on the result.
     */
    public static function determineWinner(
        AiModel|int $white,
        AiModel|int $black,
        string $result
    ): AiModel|int|null {
        if ($result === 'white') {
            return $white;
        } elseif ($result === 'black') {
            return $black;
        }

        return null; // Draw or no winner
    }

    /**
     * Get the AI model that played as white
     */
    public function white(): BelongsTo
    {
        return $this->belongsTo(AiModel::class, 'white_id');
    }

    /**
     * Get the AI model that played as black
     */
    public function black(): BelongsTo
    {
        return $this->belongsTo(AiModel::class, 'black_id');
    }

    /**
     * Get the AI model that won the match
     */
    public function winner(): BelongsTo
    {
        return $this->belongsTo(AiModel::class, 'winner_id');
    }

    /**
     * Get the AI model that lost the match
     */
    public function loser(): BelongsTo
    {
        return $this->belongsTo(AiModel::class, 'loser_id');
    }

    /**
     * Check if the match ended in a draw
     */
    public function isDraw(): bool
    {
        return $this->result === 'draw';
    }

    /**
     * Check if white won the match
     */
    public function isWhiteWinner(): bool
    {
        return $this->result === 'white';
    }

    /**
     * Check if black won the match
     */
    public function isBlackWinner(): bool
    {
        return $this->result === 'black';
    }

    /**
     * Get the total number of illegal moves made in the game
     */
    public function getTotalIllegalMoves(): int
    {
        return $this->illegal_moves_white + $this->illegal_moves_black;
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
     * Get the number of moves made (a move consists of both white and black making a move)
     *
     * This is calculated as ply_count divided by 2, rounded up for partial moves
     */
    public function getMoveCount(): int
    {
        return (int) ceil($this->ply_count / 2);
    }

    /**
     * Parse the PGN and extract all moves as a collection
     *
     * @return array<string>
     */
    public function extractMovesFromPgn(): array
    {
        $pgn = $this->pgn;

        // Remove comments
        $pgn = preg_replace('/\{[^}]*\}/', '', $pgn);

        // Remove header information
        $pgn = preg_replace('/\[[^\]]*\]\s*/', '', $pgn);

        // Remove move numbers
        $pgn = preg_replace('/\d+\.+\s*/', '', $pgn);

        // Remove final result
        $pgn = preg_replace('/\s*(1-0|0-1|1\/2-1\/2|\*)$/', '', $pgn);

        // Split into moves and trim whitespace
        $moves = array_map('trim', preg_split('/\s+/', trim($pgn)));

        // Filter out empty values
        return array_filter($moves);
    }

    /**
     * Gets the player 1 of the match (white player).
     */
    public function getPlayer1(): AiModel
    {
        return $this->white;
    }

    /**
     * Gets the player 2 of the match (black player).
     */
    public function getPlayer2(): AiModel
    {
        return $this->black;
    }

    /**
     * Gets the outcome of the match. '1' for white win, '2' for black win, 't' for tie.
     */
    public function getOutcome(): string
    {
        if ($this->result === 'white') {
            return '1';
        } elseif ($this->result === 'black') {
            return '2';
        } else {
            return 't'; // Draw
        }
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
            'white_elo_before' => $player1EloBefore,
            'black_elo_before' => $player2EloBefore,
            'white_elo_after' => $player1EloAfter,
            'black_elo_after' => $player2EloAfter,
        ]);
    }
}
