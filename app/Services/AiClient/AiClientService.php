<?php

namespace App\Services\AiClient;

use App\Services\AiClient\Concerns\AiProviderInterface;
use App\Services\AiClient\Providers\AnthropicProvider;
use App\Services\AiClient\Providers\GroqProvider;
use App\Services\AiClient\Providers\OpenAiProvider;
use App\Services\AiClient\Providers\RedpillProvider;
use Illuminate\Support\Str;

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
     * Mapping of AI model families to API endpoints and methods
     */
    protected array $providerMapping = [
        // OpenAI models
        'gpt-4o' => [
            'provider' => 'openai',
            'model' => 'gpt-4o',
        ],
        'gpt-4o-mini' => [
            'provider' => 'openai',
            'model' => 'gpt-4o-mini',
        ],
        'gpt-3.5-turbo' => [
            'provider' => 'openai',
            'model' => 'gpt-3.5-turbo-0125',
        ],
        'o1-mini' => [
            'provider' => 'openai',
            'model' => 'o1-mini',
            'direct' => true,
        ],
        'o3-mini-low' => [
            'provider' => 'openai',
            'model' => 'o3-mini',
            'reasoning_effort' => 'low',
        ],
        'o3-mini-high' => [
            'provider' => 'openai',
            'model' => 'o3-mini',
            'reasoning_effort' => 'high',
        ],

        // Anthropic models
        'claude-3-7-sonnet' => [
            'provider' => 'anthropic',
            'model' => 'claude-3-7-sonnet-20250219',
        ],
        'claude-3-7-sonnet-thinking' => [
            'provider' => 'anthropic',
            'model' => 'claude-3-7-sonnet-20250219',
            'thinking' => true,
        ],
        'claude-3-5-sonnet' => [
            'provider' => 'anthropic',
            'model' => 'claude-3-5-sonnet-20241022',
        ],

        // Groq models
        'llama3-70b-8192' => [
            'provider' => 'groq',
            'model' => 'llama3-70b-8192',
        ],
        'qwen-qwq-32b' => [
            'provider' => 'groq',
            'model' => 'qwen-qwq-32b',
        ],
        'deepseek-r1-distill-llama-70b' => [
            'provider' => 'groq',
            'model' => 'deepseek-r1-distill-llama-70b',
        ],

        // RedPill hosted models
        'deepseek-r1' => [
            'provider' => 'redpill',
            'model' => 'deepseek/deepseek-r1',
        ],
        'llama-3.1-405b-instruct' => [
            'provider' => 'redpill',
            'model' => 'meta-llama/llama-3.1-405b-instruct',
        ],
        'deepseek-v3' => [
            'provider' => 'redpill',
            'model' => 'deepseek/deepseek-chat',
        ],
        'gemini-pro-1.5' => [
            'provider' => 'redpill',
            'model' => 'google/gemini-pro-1.5',
        ],
    ];

    /**
     * Get all available models
     *
     * @return array List of available model names
     */
    public function getAvailableModels(): array
    {
        return array_keys($this->providerMapping);
    }

    /**
     * Make an API request to get a response from an AI model
     */
    public function getResponse(string $modelName, string $systemPrompt, string $userPrompt, array $config = []): mixed
    {
        $config = $this->getProviderConfig($modelName, $config);

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
        foreach ($this->providerMapping as $modelPrefix => $modelConfig) {
            if (Str::startsWith($modelName, $modelPrefix)) {
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
