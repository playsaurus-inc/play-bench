<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiModel extends Model
{
    /** @use HasFactory<\Database\Factories\AiModelFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'provider',
        'family',
        'chess_elo_rating',
        'rps_elo_rating',
        'svg_elo_rating',
    ];

    /**
     * Get the RPS matches where this model is player 1.
     */
    public function rpsMatchesAsPlayer1(): HasMany
    {
        return $this->hasMany(RpsMatch::class, 'player1_ai_model_id');
    }

    /**
     * Get the RPS matches where this model is player 2.
     */
    public function rpsMatchesAsPlayer2(): HasMany
    {
        return $this->hasMany(RpsMatch::class, 'player2_ai_model_id');
    }

    /**
     * Get the SVG matches where this model is player 1.
     */
    public function svgMatchesAsPlayer1(): HasMany
    {
        return $this->hasMany(SvgMatch::class, 'player1_ai_model_id');
    }

    /**
     * Get the SVG matches where this model is player 2.
     */
    public function svgMatchesAsPlayer2(): HasMany
    {
        return $this->hasMany(SvgMatch::class, 'player2_ai_model_id');
    }

    /**
     * Get the chess matches where this model plays white.
     */
    public function chessMatchesAsWhite(): HasMany
    {
        return $this->hasMany(ChessMatch::class, 'white_ai_model_id');
    }

    /**
     * Get the chess matches where this model plays black.
     */
    public function chessMatchesAsBlack(): HasMany
    {
        return $this->hasMany(ChessMatch::class, 'black_ai_model_id');
    }

    /**
     * Get the chess matches won by this model.
     */
    public function chessWins(): HasMany
    {
        return $this->hasMany(ChessMatch::class, 'winner_ai_model_id');
    }

    /**
     * Get all RPS matches for this model.
     */
    public function allRpsMatches(): Builder
    {
        return RpsMatch::where('player1_ai_model_id', $this->id)
            ->orWhere('player2_ai_model_id', $this->id);
    }

    /**
     * Get all SVG matches for this model.
     */
    public function allSvgMatches(): Builder
    {
        return SvgMatch::where('player1_ai_model_id', $this->id)
            ->orWhere('player2_ai_model_id', $this->id);
    }

    /**
     * Get all chess matches for this model.
     */
    public function allChessMatches(): Builder
    {
        return ChessMatch::where('white_ai_model_id', $this->id)
            ->orWhere('black_ai_model_id', $this->id);
    }
}
