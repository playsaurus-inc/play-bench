<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RpsMatch extends Model
{
    /** @use HasFactory<\Database\Factories\RpsMatchFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'player1_ai_model_id',
        'player2_ai_model_id',
        'rounds_played',
        'player1_score',
        'player2_score',
        'winner',
        'move_history',
        'is_forced_completion',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'move_history' => 'array',
        'is_forced_completion' => 'boolean',
    ];

    /**
     * Get the first player model.
     */
    public function player1(): BelongsTo
    {
        return $this->belongsTo(AiModel::class, 'player1_ai_model_id');
    }

    /**
     * Get the second player model.
     */
    public function player2(): BelongsTo
    {
        return $this->belongsTo(AiModel::class, 'player2_ai_model_id');
    }

    /**
     * Get the winner model of the match.
     */
    public function getWinnerModelAttribute(): ?AiModel
    {
        if ($this->winner === 'player1') {
            return $this->player1;
        } elseif ($this->winner === 'player2') {
            return $this->player2;
        }

        return null;
    }
}
