<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class AiModel extends Model
{
    /** @use HasFactory<\Database\Factories\AiModelFactory> */
    use HasFactory;

    /**
     * Perform any actions required before the model boots.
     *
     * @return void
     */
    protected static function booting()
    {
        static::saving(function (self $model) {
            if (! $model->slug) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    /**
     * Get the AI model from the given model, id or slug.
     */
    public static function from(AiModel|int|string $model): AiModel
    {
        if ($model instanceof AiModel) {
            return $model;
        } else if (is_numeric($model)) {
            return AiModel::findOrFail($model);
        } else {
            return AiModel::where('slug', $model)->firstOrFail();
        }
    }

    /**
     * Get the AI model ID from the given model, id or slug.
     */
    public static function idFrom(AiModel|int|string $model): int
    {
        if ($model instanceof AiModel) {
            return $model->id;
        } else if (is_numeric($model)) {
            return $model;
        } else {
            return AiModel::where('slug', $model)->firstOrFail()->id;
        }
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

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
     * Get all RPS matches where this AI model played.
     */
    public function rpsMatches(): HasMany
    {
        return $this->hasMany(RpsMatch::class, 'player1_id')
            ->orWhere('player2_id', $this->id);
    }

    /**
     * Get all RPS matches won by this AI model
     */
    public function rpsMatchesWon(): HasMany
    {
        return $this->hasMany(RpsMatch::class, 'winner_id');
    }

    /**
     * Get all RPS matches where this AI model lost
     */
    public function rpsMatchesLost(): HasMany
    {
        return $this->hasMany(RpsMatch::class, 'loser_id');
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
     * Get all SVG matches where this AI model lost
     */
    public function svgMatchesLost(): HasMany
    {
        return $this->hasMany(SvgMatch::class, 'loser_id');
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
     * Get all chess matches where this AI model lost
     */
    public function chessMatchesLost(): HasMany
    {
        return $this->hasMany(ChessMatch::class, 'loser_id');
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

    /**
     * Calculate the move breakdown of the given AI model.
     *
     * @return array<{'rock': int, 'paper': int, 'scissors': int}>
     */
    public function rpsMoveBreakdown(): array
    {
        return [
            'rock' => $this->rpsMatchesAsPlayer1()->sum('player1_move_distribution->rock') +
                $this->rpsMatchesAsPlayer2()->sum('player2_move_distribution->rock'),
            'paper' => $this->rpsMatchesAsPlayer1()->sum('player1_move_distribution->paper') +
                $this->rpsMatchesAsPlayer2()->sum('player2_move_distribution->paper'),
            'scissors' => $this->rpsMatchesAsPlayer1()->sum('player1_move_distribution->scissors') +
                $this->rpsMatchesAsPlayer2()->sum('player2_move_distribution->scissors'),
        ];
    }
}
