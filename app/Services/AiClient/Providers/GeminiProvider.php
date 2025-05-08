<?php

namespace App\Services\AiClient\Providers;

use App\Services\AiClient\Concerns\AiProviderInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiProvider implements AiProviderInterface
{
    /**
     * Create a new HTTP client instance
     */
    protected function client(): PendingRequest
    {
        return Http::withToken(config('services.gemini.key'))
            ->timeout(config('playbench.timeout'))
            ->baseUrl('https://generativelanguage.googleapis.com/v1beta/openai/');
    }

    /**
     * Call the Google Gemini API
     */
    public function handle(array $config, string $systemPrompt, string $userPrompt): string
    {
        $payload = [
            'model' => $config['model'],
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt],
            ],
        ];

        if (isset($config['reasoning_effort'])) {
            $payload['reasoning_effort'] = $config['reasoning_effort'];
        }

        $response = $this->client()->post('chat/completions', $payload);

        if (! $response->successful()) {
            Log::error('Google Gemini API error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'model' => $config['model'],
            ]);
            throw new \Exception('Google Gemini API error: '.$response->body());
        }

        $data = $response->json();

        return $data['choices'][0]['message']['content'];
    }

    /**
     * Call the Google Gemini API with images
     */
    public function handleWithImages(array $config, string $systemPrompt, string $userPrompt, array $images): string
    {
        $messageContent = [
            ['type' => 'text', 'text' => $userPrompt],
        ];

        foreach ($images as $imageUrl) {
            $messageContent[] = [
                'type' => 'image_url',
                'image_url' => ['url' => $imageUrl],
            ];
        }

        $payload = [
            'model' => $config['model'],
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $messageContent],
            ],
        ];

        $response = $this->client()->post('chat/completions', $payload);

        if (! $response->successful()) {
            Log::error('Google Gemini API error with images', [
                'status' => $response->status(),
                'body' => $response->body(),
                'model' => $config['model'],
            ]);
            throw new \Exception('Google Gemini API error with images: '.$response->body());
        }

        $data = $response->json();

        return $data['choices'][0]['message']['content'];
    }
}
