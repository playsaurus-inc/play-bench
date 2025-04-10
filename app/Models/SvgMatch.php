<?php

namespace App\Models;

use Illuminate\Contracts\Filesystem\Cloud;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class SvgMatch extends Model
{
    /** @use HasFactory<\Database\Factories\SvgMatchFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'player1_ai_model_id',
        'player2_ai_model_id',
        'winner',
        'prompt',
        'player1_svg_path',
        'player2_svg_path',
        'judge_reasoning',
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
     * Get the disk for SVG storage.
     */
    protected function disk(): Cloud
    {
        return Storage::disk('local');
    }

    /**
     * Get the URL for player 1's SVG.
     */
    public function getPlayer1SvgUrlAttribute(): string
    {
        return $this->disk()->url($this->player1_svg_path);
    }

    /**
     * Get the URL for player 2's SVG.
     */
    public function getPlayer2SvgUrlAttribute(): string
    {
        return $this->disk()->url($this->player2_svg_path);
    }

    /**
     * Get the content of player 1's SVG.
     */
    public function getPlayer1SvgContentAttribute(): string
    {
        return $this->disk()->get($this->player1_svg_path);
    }

    /**
     * Get the content of player 2's SVG.
     */
    public function getPlayer2SvgContentAttribute(): string
    {
        return $this->disk()->get($this->player2_svg_path);
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
