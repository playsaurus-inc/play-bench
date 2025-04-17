<?php

namespace App\Services\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiProvider implements AiProviderInterface
{
    /**
     * Call the OpenAI API
     */
    public function handle(array $config, string $systemPrompt, string $userPrompt): string
    {
        $client = Http::withToken(config('services.openai.key'))
            ->baseUrl('https://api.openai.com/v1');

        $payload = [
            'model' => $config['model'],
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt]
            ],
        ];

        // Add any extra model-specific parameters
        if (isset($config['reasoning_effort'])) {
            $payload['reasoning_effort'] = $config['reasoning_effort'];
        }

        if (isset($config['direct']) && $config['direct']) {
            // For models that don't support system messages
            $payload = [
                'model' => $config['model'],
                'messages' => [
                    ['role' => 'user', 'content' => $systemPrompt . " --- " . $userPrompt]
                ],
            ];
        }

        $response = $client->post('chat/completions', $payload);

        if (!$response->successful()) {
            Log::error('OpenAI API error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'model' => $config['model'],
            ]);
            throw new \Exception('OpenAI API error: ' . $response->body());
        }

        $data = $response->json();

        return $data['choices'][0]['message']['content'];
    }
}
