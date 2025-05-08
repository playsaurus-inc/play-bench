<?php

namespace App\Services\AiClient\Providers;

use App\Services\AiClient\Concerns\AiProviderInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AnthropicProvider implements AiProviderInterface
{
    /**
     * Create a new HTTP client instance
     */
    protected function client(): PendingRequest
    {
        return Http::withHeaders([
            'x-api-key' => config('services.anthropic.key'),
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])->baseUrl('https://api.anthropic.com/v1')->timeout(60);
    }

    /**
     * Call the Anthropic API
     */
    public function handle(array $config, string $systemPrompt, string $userPrompt): string
    {
        $maxTokens = $config['model'] == 'claude-3-5-sonnet-20241022' ? 8192 : 16000;

        $payload = [
            'model' => $config['model'],
            'max_tokens' => $maxTokens,
            'temperature' => 0.7,
            'system' => $systemPrompt,
            'messages' => [
                ['role' => 'user', 'content' => $userPrompt],
            ],
        ];

        if (isset($config['thinking']) && $config['thinking']) {
            $payload['thinking'] = [
                'type' => 'enabled',
                'budget_tokens' => 20000,
            ];
        }

        $response = $this->client()->post('messages', $payload);

        if (! $response->successful()) {
            Log::error('Anthropic API error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'model' => $config['model'],
            ]);
            throw new \Exception('Anthropic API error: '.$response->body());
        }

        $data = $response->json();

        // Claude with thinking returns the actual response in the second content block
        $contentIndex = isset($config['thinking']) && $config['thinking'] ? 1 : 0;

        return $data['content'][$contentIndex]['text'];
    }

    /**
     * Call the AI API with images and return the response.
     *
     * @param  array  $config  Configuration for the AI model
     * @param  string  $systemPrompt  System prompt to be sent to the AI
     * @param  string  $userPrompt  User content to be sent to the AI
     * @param  array<int, string>  $images  Images to be sent to the AI. The images should be in base64 format.
     */
    public function handleWithImages(array $config, string $systemPrompt, string $userPrompt, array $images): string
    {
        throw new \Exception('Image processing not yet implemented for Anthropic provider');
    }
}
