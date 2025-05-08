<?php

namespace App\Services\AiClient\Providers;

use App\Services\AiClient\Concerns\AiProviderInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RedpillProvider implements AiProviderInterface
{
    /**
     * Create a new HTTP client instance
     */
    protected function client(): PendingRequest
    {
        return Http::withToken(config('services.redpill.key'))
            ->timeout(config('playbench.timeout'))
            ->baseUrl('https://api.red-pill.ai/v1');
    }

    /**
     * Call the RedPill API (which provides access to various models)
     */
    public function handle(array $config, string $systemPrompt, string $userPrompt): string
    {
        $payload = [
            'model' => $config['model'],
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt],
            ],
            'temperature' => 0.7,
        ];

        $response = $this->client()->post('chat/completions', $payload);

        if (! $response->successful()) {
            Log::error('RedPill API error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'model' => $config['model'],
            ]);
            throw new \Exception('RedPill API error: '.$response->body());
        }

        $data = $response->json();

        // Process the response based on the game type
        return $data['choices'][0]['message']['content'];
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
        throw new \Exception('RedPill does not support images yet.');
    }
}
