<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChessMatch extends Model
{
    /** @use HasFactory<\Database\Factories\ChessMatchFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'white_ai_model_id',
        'black_ai_model_id',
        'moves_count',
        'winner_color',
        'winner_ai_model_id',
        'pgn',
        'final_fen',
        'illegal_moves_white',
        'illegal_moves_black',
        'is_forced_completion',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_forced_completion' => 'boolean',
    ];

    /**
     * Get the model playing white.
     */
    public function whiteModel(): BelongsTo
    {
        return $this->belongsTo(AiModel::class, 'white_ai_model_id');
    }

    /**
     * Get the model playing black.
     */
    public function blackModel(): BelongsTo
    {
        return $this->belongsTo(AiModel::class, 'black_ai_model_id');
    }

    /**
     * Get the winner model.
     */
    public function winnerModel(): BelongsTo
    {
        return $this->belongsTo(AiModel::class, 'winner_ai_model_id');
    }
}
