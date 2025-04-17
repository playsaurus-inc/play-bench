<?php

namespace App\Services\Providers;

interface AiProviderInterface
{
    /**
     * Call the AI API and return the response.
     *
     * @param array $config Configuration for the AI model
     * @param string $systemPrompt System prompt to be sent to the AI
     * @param string $userPrompt User prompt to be sent to the AI
     * @return string The response from the AI
     */
    public function handle(array $config, string $systemPrompt, string $userPrompt): string;
}
