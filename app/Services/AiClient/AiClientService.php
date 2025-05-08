<?php

namespace App\Services\AiClient;

use App\Services\AiClient\Concerns\AiProviderInterface;
use App\Services\AiClient\Providers\AnthropicProvider;
use App\Services\AiClient\Providers\GroqProvider;
use App\Services\AiClient\Providers\OpenAiProvider;
use App\Services\AiClient\Providers\RedpillProvider;

class AiClientService
{
    /**
     * The number of times to retry a failed request
     */
    protected int $retryCount = 3;

    /**
     * The milliseconds to wait between retries
     */
    protected int $retryDelay = 1000;

    /**
     * Get all available models for the specified game.
     *
     * @return array List of available model names
     */
    public function getAvailableModels(string $game): array
    {
        return collect($this->models())
            ->filter(fn ($model) => $model['games'] === '*' || in_array($game, $model['games']))
            ->keys()
            ->toArray();
    }

    /**
     * Gets the configuration for the AI models.
     */
    public function models(): array
    {
        return config('playbench.models');
    }

    /**
     * Make an API request to get a response from an AI model
     */
    public function getResponse(string $model, string $systemPrompt, string $userPrompt, array $config = []): mixed
    {
        $config = $this->getProviderConfig($model, $config);

        $provider = $this->getProvider($config['provider']);

        return $this->normalizeResponse(retry(
            times: $this->retryCount,
            callback: fn () => $provider->handle($config, $systemPrompt, $userPrompt),
            sleepMilliseconds: $this->retryDelay,
        ));
    }

    /**
     * Make an API request with images to get a response from an AI model
     */
    public function getResponseWithImages(string $modelName, string $systemPrompt, string $userPrompt, array $images, array $config = []): string
    {
        $config = $this->getProviderConfig($modelName, $config);

        $provider = $this->getProvider($config['provider']);

        return $this->normalizeResponse(retry(
            times: $this->retryCount,
            callback: fn () => $provider->handleWithImages($config, $systemPrompt, $userPrompt, $images),
            sleepMilliseconds: $this->retryDelay,
        ));
    }

    /**
     * Get the configuration for a specific model.
     */
    protected function getProviderConfig(string $modelName, array $extraConfig = []): array
    {
        foreach ($this->models() as $modelSlug => $modelConfig) {
            if ($modelSlug == $modelName) {
                return array_merge($modelConfig, $extraConfig);
            }
        }

        throw new \Exception("No API configuration found for model: {$modelName}");
    }

    /**
     * Get the provider instance based on the provider name
     */
    protected function getProvider(string $providerName): AiProviderInterface
    {
        return match ($providerName) {
            'openai' => new OpenAiProvider,
            'anthropic' => new AnthropicProvider,
            'groq' => new GroqProvider,
            'redpill' => new RedpillProvider,
            default => throw new \Exception("No provider found for: {$providerName}"),
        };
    }

    /**
     * Normalize the response from the AI model
     */
    protected function normalizeResponse(string $response): string
    {
        // Remove markdown code blocks that some models might add
        $response = preg_replace('/```(?:json|svg)?\s*(.+?)\s*```/s', '$1', $response);

        // Some models might include a <think>...</think> block, remove it
        if (preg_match('/<think>(.*?)<\/think>/s', $response, $matches)) {
            $response = str_replace($matches[0], '', $response);
        }

        return $response;
    }
}
