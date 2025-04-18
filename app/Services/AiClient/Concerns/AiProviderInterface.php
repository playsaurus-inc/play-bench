<?php

namespace App\Services\AiClient\Concerns;

interface AiProviderInterface
{
    /**
     * Call the AI API and return the response.
     *
     * @param  array  $config  Configuration for the AI model
     * @param  string  $systemPrompt  System prompt to be sent to the AI
     * @param  string  $userPrompt  User prompt to be sent to the AI
     * @return string The response from the AI
     */
    public function handle(array $config, string $systemPrompt, string $userPrompt): string;

    /**
     * Call the AI API with images and return the response.
     *
     * @param  array  $config  Configuration for the AI model
     * @param  string  $systemPrompt  System prompt to be sent to the AI
     * @param  string  $userPrompt  User content to be sent to the AI
     * @param  array<int, string>  $images  Images to be sent to the AI. The images should be in base64 format.
     */
    public function handleWithImages(array $config, string $systemPrompt, string $userPrompt, array $images): string;
}
