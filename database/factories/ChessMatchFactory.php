<?php

namespace Database\Factories;

use App\Models\AiModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ChessMatch>
 */
class ChessMatchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'white_id' => AiModel::factory(),
            'black_id' => AiModel::factory(),
            'ply_count' => $this->faker->numberBetween(10, 40),
            'result' => $this->faker->randomElement(['white', 'black', 'draw']),
            'pgn' => '1.e4 e5 2.Nf3 Nc6 3.Bb5 a6 4.Ba4 Nf6 5.O-O Be7 6.Re1 d6 7.c3 O-O 8.h3 Nb8 9.d4 Nbd7 10.Nbd2 c6 11.Bc2 Qc7 12.Nf1 Re8 13.Ng3 Bf8 14.Nh4 g6 15.f4 Bg7 16.f5 Nf8 17.Rf1 Bd7 18.Qf3 Qd8 19.Bg5 h6 20.Be3 g5', // Placeholder PGN
            'final_fen' => 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1', // Starting position
            'illegal_moves_white' => $this->faker->numberBetween(0, 3),
            'illegal_moves_black' => $this->faker->numberBetween(0, 3),
            'is_forced_completion' => $this->faker->boolean(10),
            'started_at' => $this->faker->dateTimeBetween('-1 month', '-1 day'),
            'ended_at' => $this->faker->dateTimeBetween('-1 day', 'now'),
        ];
    }

    /**
     * Configure the model factory with specific players
     */
    public function withPlayers(AiModel $white, AiModel $black): self
    {
        return $this->state([
            'white_id' => $white->id,
            'black_id' => $black->id,
        ]);
    }
}
