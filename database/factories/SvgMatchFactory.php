<?php

namespace Database\Factories;

use App\Models\AiModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SvgMatch>
 */
class SvgMatchFactory extends Factory
{
    /**
     * The prompts to use for the SVG matches.
     *
     * @var array<string, string>
     */
    protected array $prompts = [
        'Draw a cyberpunk cityscape',
        'Create a futuristic car design',
        'Draw a serene mountain landscape',
        'Illustrate a fantasy creature',
        'Design a minimalist logo',
        'A robot playing chess',
        'Magic forest with glowing plants',
        'A steampunk airship',
        'A dragon flying over a castle',
        'A futuristic city skyline at sunset',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $matchId = $this->faker->uuid;

        return [
            'player1_id' => AiModel::factory(),
            'player2_id' => AiModel::factory(),
            'winner_id' => function (array $attributes) {
                // 80% chance of having a winner, 20% chance of null
                return $this->faker->boolean(80)
                    ? $this->faker->randomElement([$attributes['player1_id'], $attributes['player2_id']])
                    : null;
            },
            'prompt' => $this->faker->randomElement($this->prompts),
            'player1_svg_path' => "svg-match/{$matchId}-player1.svg",
            'player2_svg_path' => "svg-match/{$matchId}-player2.svg",
            'judge_reasoning' => $this->faker->paragraph,
            'started_at' => $this->faker->dateTimeBetween('-1 month', '-1 day'),
            'ended_at' => $this->faker->dateTimeBetween('-1 day', 'now'),
        ];
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
     * Configure the model factory to save real SVGs in the file system
     */
    public function withRealSvgs(string $svgContent1, string $svgContent2): self
    {
        return $this->afterCreating(function ($svgMatch) use ($svgContent1, $svgContent2) {
            Storage::disk('public')->put($svgMatch->player1_svg_path, $svgContent1);
            Storage::disk('public')->put($svgMatch->player2_svg_path, $svgContent2);
        });
    }

    /**
     * Configure the model factory to save fake SVGs in the file system
     */
    public function withFakeSvgs(): self
    {
        return $this->withRealSvgs($this->svg(), $this->svg());
    }

    /**
     * Creates a sample SVG string.
     */
    protected function svg(): string
    {
        $label = $this->faker->word();
        $color = $this->faker->hexColor();

        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 300">
            <rect width="300" height="300" fill="'.$color.'"/>
            <text x="150" y="150" text-anchor="middle" fill="black">'.$label.'</text>
        </svg>';
    }
}
