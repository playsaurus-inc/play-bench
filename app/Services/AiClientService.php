<?php

namespace App\Services;

use App\Models\AiModel;
use App\Services\Providers\AiProviderInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AiClientService
{
    /**
     * Mapping of AI model families to API endpoints and methods
     */
    protected array $providerMapping = [
        // OpenAI models
        //'gpt-4o' => [
        //    'provider' => 'openai',
        //    'model' => 'gpt-4o',
        //],
        //'gpt-4o-mini' => [
        //    'provider' => 'openai',
        //    'model' => 'gpt-4o-mini',
        //],
        //'gpt-3.5-turbo' => [
        //    'provider' => 'openai',
        //    'model' => 'gpt-3.5-turbo-0125',
        //],
        //'o1-mini' => [
        //    'provider' => 'openai',
        //    'model' => 'o1-mini',
        //    'direct' => true,
        //],
        //'o3-mini-low' => [
        //    'provider' => 'openai',
        //    'model' => 'o3-mini',
        //    'reasoning_effort' => 'low',
        //],
        //'o3-mini-high' => [
        //    'provider' => 'openai',
        //    'model' => 'o3-mini',
        //    'reasoning_effort' => 'high',
        //],

        // Anthropic models
        //'claude-3-7-sonnet' => [
        //    'provider' => 'anthropic',
        //    'model' => 'claude-3-7-sonnet-20250219',
        //],
        //'claude-3-7-sonnet-thinking' => [
        //    'provider' => 'anthropic',
        //    'model' => 'claude-3-7-sonnet-20250219',
        //    'thinking' => true,
        //],
        //'claude-3-5-sonnet' => [
        //    'provider' => 'anthropic',
        //    'model' => 'claude-3-5-sonnet-20241022',
        //],

        // Groq models
        //'llama3-70b-8192' => [
        //    'provider' => 'groq',
        //    'model' => 'llama3-70b-8192',
        //],
        //'qwen-2.5-32b' => [ // DEPRECATED IN FAVOR OF qwen-qwq-32b
        //    'provider' => 'groq',
        //    'model' => 'qwen-2.5-32b',
        //],
        //'deepseek-r1-distill-llama-70b' => [
        //    'provider' => 'groq',
        //    'model' => 'deepseek-r1-distill-llama-70b',
        //],
        //'deepseek-r1-distill-qwen-32b' => [ // DEPRECATED in favor of qwen-qwq-32b
        //    'provider' => 'groq',
        //    'model' => 'deepseek-r1-distill-qwen-32b',
        //],
        'qwen-qwq-32b' => [
            'provider' => 'groq',
            'model' => 'qwen-qwq-32b',
        ],

        // RedPill hosted models
        //'deepseek-r1' => [
        //    'provider' => 'redpill',
        //    'model' => 'deepseek/deepseek-r1',
        //],
        //'llama-3.1-405b-instruct' => [
        //    'provider' => 'redpill',
        //    'model' => 'meta-llama/llama-3.1-405b-instruct',
        //],
        //'deepseek-v3' => [
        //    'provider' => 'redpill',
        //    'model' => 'deepseek/deepseek-chat',
        //],
        //'gemini-pro-1.5' => [
        //    'provider' => 'redpill',
        //    'model' => 'google/gemini-pro-1.5',
        //],

        // Random baseline
        'random' => [
            'provider' => 'random',
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
    public function getResponse(string $modelName, string $systemPrompt, string $userPrompt, string $game): mixed
    {
        // Get the first matching model configuration
        $config = null;
        foreach ($this->providerMapping as $modelPrefix => $modelConfig) {
            if (Str::startsWith($modelName, $modelPrefix)) {
                $config = $modelConfig;
                break;
            }
        }

        if (!$config) {
            throw new \Exception("No API configuration found for model: {$modelName}");
        }

        $provider = $this->getProvider($config['provider'], $game);

        return $this->processResponse(
            $provider->handle($config, $systemPrompt, $userPrompt),
            $game
        );
    }

    /**
     * Get the provider instance based on the provider name
     */
    protected function getProvider(string $providerName, string $game): AiProviderInterface
    {
        return match ($providerName) {
            'openai' => new Providers\OpenAiProvider(),
            'anthropic' => new Providers\AnthropicProvider(),
            'groq' => new Providers\GroqProvider(),
            'redpill' => new Providers\RedpillProvider(),
            'random' => new Providers\RandomProvider($game),
            default => throw new \Exception("No provider found for: {$providerName}"),
        };
    }

    /**
     * Process the raw model response based on the game type
     */
    protected function processResponse(string $response, string $game): string
    {
        // Remove markdown code blocks that some models might add
        $response = preg_replace('/```(?:json|svg)?\s*(.+?)\s*```/s', '$1', $response);

        // Some models might include a <think>...</think> block, remove it
        if (Str::contains($response, '<think>')) {
            $response = preg_replace('/<think>.*?<\/think>/', '', $response);
        }

        if ($game === 'rps') {
            // Extract the move from a JSON response
            if (preg_match('/"move"\s*:\s*"([^"]+)"/', $response, $matches)) {
                return strtolower($matches[1]);
            }

            // If not in JSON format, look for "rock", "paper", or "scissors" keywords
            if (preg_match('/\b(rock|paper|scissors)\b/i', $response, $matches)) {
                return strtolower($matches[1]);
            }

            throw new \Exception("Could not parse RPS move from response: " . $response);
        }

        if ($game === 'chess') {
            // Extract the move from a JSON response
            if (preg_match('/"move"\s*:\s*"([^"]+)"/', $response, $matches)) {
                return $matches[1];
            }

            // Look for a UCI format move (e.g., e2e4)
            if (preg_match('/\b([a-h][1-8][a-h][1-8][qrbnk]?)\b/', $response, $matches)) {
                return $matches[1];
            }

            throw new \Exception("Could not parse chess move from response: " . $response);
        }

        if ($game === 'svg') {
            // Look for SVG content
            if (preg_match('/<svg.*<\/svg>/s', $response, $matches)) {
                return $matches[0];
            }

            throw new \Exception("Could not find SVG content in response");
        }

        return $response;
    }
}
