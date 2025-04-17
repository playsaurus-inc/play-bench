<?php

namespace App\Services\Providers;

class RandomProvider implements AiProviderInterface
{
    /**
     * Creates an instance of the RandomProvider
     */
    public function __construct(
        private string $game,
    ) { }

    /**
     * Handle random model response generation
     */
    public function handle(array $config, string $systemPrompt, string $userPrompt): string
    {
        if ($this->game === 'rps') {
            return ['rock', 'paper', 'scissors'][random_int(0, 2)];
        }

        if ($this->game === 'chess') {
            // Return a random UCI move format (dummy - actual legal moves would be validated by the chess service)
            $files = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'];
            $ranks = ['1', '2', '3', '4', '5', '6', '7', '8'];

            $from = $files[random_int(0, 7)] . $ranks[random_int(0, 7)];
            $to = $files[random_int(0, 7)] . $ranks[random_int(0, 7)];

            return $from . $to;
        }

        if ($this->game === 'svg') {
            // Return a simple SVG circle
            $color = dechex(random_int(0, 16777215)); // Random hex color
            $r = random_int(30, 100);
            $cx = random_int(100, 200);
            $cy = random_int(100, 200);

            return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 300"><circle cx="'.$cx.'" cy="'.$cy.'" r="'.$r.'" fill="#'.$color.'"/></svg>';
        }

        throw new \Exception("Game not supported for random model: {$this->game}");
    }
}
