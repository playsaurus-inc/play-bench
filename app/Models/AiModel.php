<?php

namespace App\Models;

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
    ];

    /**
     * Get all RPS matches where this AI model is player 1
     */
    public function rpsMatchesAsPlayer1(): HasMany
    {
        return $this->hasMany(RpsMatch::class, 'player1_id');
    }

    /**
     * Get all RPS matches where this AI model is player 2
     */
    public function rpsMatchesAsPlayer2(): HasMany
    {
        return $this->hasMany(RpsMatch::class, 'player2_id');
    }

    /**
     * Get all RPS matches won by this AI model
     */
    public function rpsMatchesWon(): HasMany
    {
        return $this->hasMany(RpsMatch::class, 'winner_id');
    }

    /**
     * Get all SVG matches where this AI model is player 1
     */
    public function svgMatchesAsPlayer1(): HasMany
    {
        return $this->hasMany(SvgMatch::class, 'player1_id');
    }

    /**
     * Get all SVG matches where this AI model is player 2
     */
    public function svgMatchesAsPlayer2(): HasMany
    {
        return $this->hasMany(SvgMatch::class, 'player2_id');
    }

    /**
     * Get all SVG matches won by this AI model
     */
    public function svgMatchesWon(): HasMany
    {
        return $this->hasMany(SvgMatch::class, 'winner_id');
    }

    /**
     * Get all chess matches where this AI model plays as white
     */
    public function chessMatchesAsWhite(): HasMany
    {
        return $this->hasMany(ChessMatch::class, 'white_id');
    }

    /**
     * Get all chess matches where this AI model plays as black
     */
    public function chessMatchesAsBlack(): HasMany
    {
        return $this->hasMany(ChessMatch::class, 'black_id');
    }

    /**
     * Get all chess matches won by this AI model
     */
    public function chessMatchesWon(): HasMany
    {
        return $this->hasMany(ChessMatch::class, 'winner_id');
    }

    /**
     * Get all RPS matches this AI model participated in
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allRpsMatches()
    {
        return $this->rpsMatchesAsPlayer1->merge($this->rpsMatchesAsPlayer2);
    }

    /**
     * Get all SVG matches this AI model participated in
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allSvgMatches()
    {
        return $this->svgMatchesAsPlayer1->merge($this->svgMatchesAsPlayer2);
    }

    /**
     * Get all chess matches this AI model participated in
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allChessMatches()
    {
        return $this->chessMatchesAsWhite->merge($this->chessMatchesAsBlack);
    }
}
