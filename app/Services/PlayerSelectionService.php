<?php

namespace App\Services;

use App\Models\AiModel;
use App\Models\ChessMatch;
use App\Models\RpsMatch;
use App\Models\SvgMatch;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class PlayerSelectionService
{
    /**
     * Returns a collection of all unique player pairs, sorted by the number of matches
     * played against each other in ascending order.
     *
     * @param  string  $gameType  The type of game (e.g., 'rps', 'chess', 'svg').
     * @param  iterable<AiModel>|null  $aiModels  The AI models to consider for selection. By default, all models (excluding 'random' slug) are used.
     * @return Collection<int, Matchup> A collection of Matchup objects representing the player pairs.
     */
    public function get(string $gameType, ?iterable $aiModels = null): Collection
    {
        $aiModels = collect($aiModels ?? AiModel::all())
            ->reject(fn (AiModel $model) => $model->slug === 'random');

        if ($aiModels->count() < 2) {
            return collect(); // Not enough models to form a pair
        }

        $countByPairs = $this->getMatches($gameType)
            ->filter(fn ($match) => isset($match->player1_id, $match->player2_id)) // Ensure player IDs are not null
            ->groupBy(fn ($match) => $this->getKey($match->player1_id, $match->player2_id))
            ->map(fn (Collection $group) => $group->count());

        return $aiModels
            ->crossJoin($aiModels)
            // Remove self-pairs (A vs A) and duplicate pairs (B vs A if A vs B exists)
            // Ensures player1.id < player2.id for unique pairs
            ->reject(fn (array $pair) => $pair[0]->id >= $pair[1]->id)
            ->mapSpread(fn (AiModel $p1, AiModel $p2) => new Matchup(
                player1: $p1,
                player2: $p2,
                matchesPlayed: $countByPairs->get($this->getKey($p1->id, $p2->id), 0),
                random: true,
            ))
            ->sortBy('matchesPlayed')
            ->values(); // Re-index the collection
    }

    /**
     * Returns the player pair with the least number of matches played.
     *
     * @param  string  $gameType  The type of game (e.g., 'rps', 'chess', 'svg').
     * @param  iterable<AiModel>|null  $aiModels  The AI models to consider for selection. By default, all models (excluding 'random' slug) are used.
     * @return Matchup|null The selected player pair with the least number of matches played, or null if no pairs are found.
     */
    public function first(string $gameType, ?iterable $aiModels = null): ?Matchup
    {
        return $this->get($gameType, $aiModels)->first();
    }

    /**
     * Gets all matches for the given game type, selecting only player IDs.
     *
     * @param  string  $gameType  The type of game (e.g., 'rps', 'svg', 'chess').
     * @return Collection<int, object{player1_id: int, player2_id: int}>
     *
     * @throws InvalidArgumentException If the game type is invalid.
     */
    protected function getMatches(string $gameType): Collection
    {
        return match ($gameType) {
            'rps' => RpsMatch::select('player1_id', 'player2_id')->get(),
            'svg' => SvgMatch::select('player1_id', 'player2_id')->get(),
            'chess' => ChessMatch::select('white_id as player1_id', 'black_id as player2_id')->get(),
            default => throw new InvalidArgumentException("Invalid game type: {$gameType}"),
        };
    }

    /**
     * Gets a canonical key for the player pair (e.g., "1-2" not "2-1").
     *
     * @param  int  $player1Id  The ID of the first player.
     * @param  int  $player2Id  The ID of the second player.
     * @return string The canonical key for the pair.
     */
    protected function getKey(int $player1Id, int $player2Id): string
    {
        return $player1Id < $player2Id
            ? "{$player1Id}-{$player2Id}"
            : "{$player2Id}-{$player1Id}";
    }

    /**
     * Creates a matchup object between two players.
     */
    public function matchup(string $gameType, AiModel $player1, AiModel $player2): Matchup
    {
        $matches = $this->getMatches($gameType);

        // This is super inefficient, but whatever
        $matchesAB = $matches
            ->where('player1_id', $player1->id)
            ->where('player2_id', $player2->id)
            ->count();

        $matchesBA = $matches
            ->where('player1_id', $player2->id)
            ->where('player2_id', $player1->id)
            ->count();

        return new Matchup(
            player1: $player1,
            player2: $player2,
            matchesPlayed: $matchesAB + $matchesBA,
            random: true,
        );
    }

    /**
     * Creates a matchup object between two random players.
     */
    public function random(string $gameType, ?iterable $aiModels = null): Matchup
    {
        $aiModels = collect($aiModels ?? AiModel::all())
            ->reject(fn (AiModel $model) => $model->slug === 'random');

        if ($aiModels->count() < 2) {
            return null; // Not enough models to form a pair
        }

        $player1 = $aiModels->random();
        $player2 = $aiModels->where('id', '!=', $player1->id)->random();

        return new Matchup(
            player1: $player1,
            player2: $player2,
            matchesPlayed: 0,
            random: true,
        );
    }
}
