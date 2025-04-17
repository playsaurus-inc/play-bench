<?php

namespace App\Services\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AnthropicProvider implements AiProviderInterface
{
    /**
     * Call the Anthropic API
     */
    public function handle(array $config, string $systemPrompt, string $userPrompt): string
    {
        $client = Http::withHeaders([
            'x-api-key' => config('services.anthropic.key'),
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json'
        ])->baseUrl('https://api.anthropic.com/v1');

        $maxTokens = $config['model'] == 'claude-3-5-sonnet-20241022' ? 8192 : 16000;

        $payload = [
            'model' => $config['model'],
            'max_tokens' => $maxTokens,
            'temperature' => 0.7,
            'system' => $systemPrompt,
            'messages' => [
                ['role' => 'user', 'content' => $userPrompt]
            ]
        ];

        if (isset($config['thinking']) && $config['thinking']) {
            $payload['thinking'] = [
                'type' => 'enabled',
                'budget_tokens' => 20000
            ];
        }

        $response = $client->post('messages', $payload);

        if (!$response->successful()) {
            Log::error('Anthropic API error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'model' => $config['model'],
            ]);
            throw new \Exception('Anthropic API error: ' . $response->body());
        }

        $data = $response->json();

        // Claude with thinking returns the actual response in the second content block
        $contentIndex = isset($config['thinking']) && $config['thinking'] ? 1 : 0;

        return $data['content'][$contentIndex]['text'];
    }
}


