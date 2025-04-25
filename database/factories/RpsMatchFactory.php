<?php

namespace Database\Factories;

use App\Models\AiModel;
use App\Services\Rps\RpsRound;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RpsMatch>
 */
class RpsMatchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $rounds = $this->faker->numberBetween(10, 100);

        $moveHistory = $this->generateMoveHistory($rounds);

        $startedAt = now()->subMinutes(rand(10, 60));
        $endedAt = now()->subMinutes(rand(1, 9));

        return [
            'player1_id' => AiModel::factory(),
            'player2_id' => AiModel::factory(),
            'rounds_played' => $rounds,
            'player1_score' => Str::substrCount($moveHistory, '1'),
            'player2_score' => Str::substrCount($moveHistory, '2'),
            'move_history' => $moveHistory,
            'started_at' => $startedAt,
            'ended_at' => $endedAt,
            'is_forced_completion' => false,
        ];
    }

    /**
     * Generate a realistic move history string for the match.
     */
    protected function generateMoveHistory(int $rounds): string
    {
        $moves = collect(['r', 'p', 's']);

        return Collection::times($rounds)
            ->map(fn () => [$moves->random(), $moves->random()])
            ->map(fn ($move) => (string) new RpsRound($move[0], $move[1]))
            ->implode(' ');
    }

    /**
     * Configure the model factory with specific players
     */
    public function withPlayers(AiModel $player1, AiModel $player2): self
    {
        return $this->state([
            'player1_id' => $player1->id,
            'player2_id' => $player2->id,
        ]);
    }

    /**
     * Configure the model factory to mark the match as forcibly completed
     */
    public function forcedCompletion(): self
    {
        return $this->state([
            'is_forced_completion' => true,
        ]);
    }
}
