<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class SvgMatch extends Model
{
    /** @use HasFactory<\Database\Factories\SvgMatchFactory> */
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
     * Get Player 1's SVG content
     */
    public function getPlayer1SvgContent(): ?string
    {
        if (! $this->player1_svg_path || ! Storage::exists($this->player1_svg_path)) {
            return null;
        }

        return Storage::get($this->player1_svg_path);
    }

    /**
     * Get Player 2's SVG content
     */
    public function getPlayer2SvgContent(): ?string
    {
        if (! $this->player2_svg_path || ! Storage::exists($this->player2_svg_path)) {
            return null;
        }

        return Storage::get($this->player2_svg_path);
    }

    /**
     * Get the full URL to player 1's SVG
     */
    public function getPlayer1SvgUrl(): ?string
    {
        if (! $this->player1_svg_path) {
            return null;
        }

        return Storage::url($this->player1_svg_path);
    }

    /**
     * Get the full URL to player 2's SVG
     */
    public function getPlayer2SvgUrl(): ?string
    {
        if (! $this->player2_svg_path) {
            return null;
        }

        return Storage::url($this->player2_svg_path);
    }

    /**
     * Check if player 1 is the winner
     */
    public function isPlayer1Winner(): bool
    {
        return $this->winner_id === $this->player1_id;
    }

    /**
     * Check if player 2 is the winner
     */
    public function isPlayer2Winner(): bool
    {
        return $this->winner_id === $this->player2_id;
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
}
