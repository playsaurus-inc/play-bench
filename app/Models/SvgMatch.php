<?php

namespace App\Models;

use App\Models\Contracts\RankedMatch;
use App\Services\Svg\SvgAnalysisService;
use Illuminate\Contracts\Filesystem\Cloud;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class SvgMatch extends Model implements RankedMatch
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
        'player1_elo_before' => 'float',
        'player2_elo_before' => 'float',
        'player1_elo_after' => 'float',
        'player2_elo_after' => 'float',
        'player1_features' => 'array',
        'player2_features' => 'array',
    ];

    /**
     * Perform any actions required before the model boots.
     *
     * @return void
     */
    protected static function booting()
    {
        static::saving(function (self $model) {
            if (! isset($model->loser_id)) {
                $model->loser_id = match ($model->winner_id) {
                    $model->player1_id => $model->player2_id,
                    $model->player2_id => $model->player1_id,
                    default => null, // Should not happen if everything works ok
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
            'loser_id' => null,
        ]);
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
     * Get the AI model that lost the match
     */
    public function loser(): BelongsTo
    {
        return $this->belongsTo(AiModel::class, 'loser_id');
    }

    /**
     * Get the disk used to store the SVG files.
     */
    public function disk(): Cloud
    {
        return Storage::disk('svg');
    }

    /**
     * Get Player 1's SVG content
     */
    public function getPlayer1SvgContent(): ?string
    {
        if (! $this->player1_svg_path || ! $this->disk()->exists($this->player1_svg_path)) {
            return null;
        }

        return $this->disk()->get($this->player1_svg_path);
    }

    /**
     * Get Player 2's SVG content
     */
    public function getPlayer2SvgContent(): ?string
    {
        if (! $this->player2_svg_path || ! $this->disk()->exists($this->player2_svg_path)) {
            return null;
        }

        return $this->disk()->get($this->player2_svg_path);
    }

    /**
     * Get the full URL to player 1's SVG
     */
    public function getPlayer1SvgUrl(): ?string
    {
        if (! $this->player1_svg_path) {
            return null;
        }

        return $this->disk()->url($this->player1_svg_path);
    }

    /**
     * Get the full URL to player 2's SVG
     */
    public function getPlayer2SvgUrl(): ?string
    {
        if (! $this->player2_svg_path) {
            return null;
        }

        return $this->disk()->url($this->player2_svg_path);
    }

    /**
     * Get the full URL to the winner's SVG
     */
    public function getWinnerSvgUrl(): ?string
    {
        return $this->winner_id === $this->player1_id
            ? $this->getPlayer1SvgUrl()
            : $this->getPlayer2SvgUrl();
    }

    /**
     * Get the full URL to the loser's SVG
     */
    public function getLoserSvgUrl(): ?string
    {
        return $this->loser_id === $this->player1_id
            ? $this->getPlayer1SvgUrl()
            : $this->getPlayer2SvgUrl();
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

    /**
     * Gets the player 1 of the match.
     */
    public function getPlayer1(): AiModel
    {
        return $this->player1;
    }

    /**
     * Gets the player 2 of the match.
     */
    public function getPlayer2(): AiModel
    {
        return $this->player2;
    }

    /**
     * Scopes the query to only include matches with a specific player.
     */
    #[Scope]
    public function playedBy(Builder $query, AiModel|int|string $player): void
    {
        $player = AiModel::idFrom($player);

        $query->where(function ($q) use ($player) {
            return $q->where('player1_id', $player)->orWhere('player2_id', $player);
        });
    }

    /**
     * Scopes the query to only include matches where two players played against each other.
     */
    #[Scope]
    protected function playedAgainst(Builder $query, AiModel|int|string $player1, AiModel|int|string $player2): void
    {
        $player1 = AiModel::idFrom($player1);
        $player2 = AiModel::idFrom($player2);

        $query->where(function ($q) use ($player1, $player2) {
            return $q->where(function ($inner) use ($player1, $player2) {
                $inner->where('player1_id', $player1)->where('player2_id', $player2);
            })->orWhere(function ($inner) use ($player1, $player2) {
                $inner->where('player1_id', $player2)->where('player2_id', $player1);
            });
        });
    }

    /**
     * Gets the outcome of the match. '1' for player 1 win, '2' for player 2 win, 't' for tie.
     */
    public function getOutcome(): string
    {
        if ($this->winner_id === $this->player1_id) {
            return '1';
        } elseif ($this->winner_id === $this->player2_id) {
            return '2';
        } else {
            return 't'; // Though SVG matches typically don't have ties
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
            'player1_elo_before' => $player1EloBefore,
            'player2_elo_before' => $player2EloBefore,
            'player1_elo_after' => $player1EloAfter,
            'player2_elo_after' => $player2EloAfter,
        ]);
    }

    /**
     * Gets the SVG features for the player 1.
     */
    public function getPlayer1SvgFeatures(): ?array
    {
        return app(SvgAnalysisService::class)->getFeatureDescriptions($this->player1_features);
    }

    /**
     * Gets the SVG features for the player 2.
     */
    public function getPlayer2SvgFeatures(): ?array
    {
        return app(SvgAnalysisService::class)->getFeatureDescriptions($this->player2_features);
    }

    /**
     * Gets a comparative analysis of the SVG features between player 1 and player 2.
     */
    public function getComparativeSvgFeatures(): ?array
    {
        $player1 = collect($this->getPlayer1SvgFeatures());
        $player2 = collect($this->getPlayer2SvgFeatures());

        $player1Values = $player1->map(fn ($value) => $value['value']);
        $player2Values = $player2->map(fn ($value) => $value['value']);

        return $player1->merge($player2)
            ->map(fn ($feature, $key) => [
                'name' => $feature['name'],
                'value' => $feature['value'],
                'description' => $feature['description'],
                'category' => $feature['category'],
                'player1_value' => $player1Values[$key] ?? null,
                'player2_value' => $player2Values[$key] ?? null,
                'delta' => is_numeric($player1Values[$key]) && is_numeric($player2Values[$key])
                    ? $player1Values[$key] - $player2Values[$key]
                    : null,
            ])
            ->all();
    }
}
