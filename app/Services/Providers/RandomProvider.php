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


        if ($this->game === 'svg') {

        }

        throw new \Exception("Game not supported for random model: {$this->game}");
    }

    /**
     * Call the AI API with images and return the response.
     *
     * @param array $config Configuration for the AI model
     * @param string $systemPrompt System prompt to be sent to the AI
     * @param string $userPrompt User content to be sent to the AI
     * @param array<int, string> $images Images to be sent to the AI. The images should be in base64 format.
     */
    public function handleWithImages(array $config, string $systemPrompt, string $userPrompt, array $images): string
    {
        if ($this->game === 'image') {
            // For image judging, randomly pick player 1 or player 2
            $winner = random_int(0, 1) === 0 ? 'player1' : 'player2';
            $reason = "Random judgment: Selected {$winner} arbitrarily.";

            return json_encode([
                'winner' => $winner,
                'reason' => $reason
            ]);
        }

        return $this->handle($config, $systemPrompt, $userPrompt);
    }
}
