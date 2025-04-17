<?php

namespace App\Services\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RedpillProvider implements AiProviderInterface
{

    /**
     * Call the RedPill API (which provides access to various models)
     */
    public function handle(array $config, string $systemPrompt, string $userPrompt): string
    {
        $client = Http::withToken(config('services.redpill.key'))
            ->baseUrl('https://api.red-pill.ai/v1');

        $payload = [
            'model' => $config['model'],
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt]
            ],
            'temperature' => 0.7
        ];

        $response = $client->post('chat/completions', $payload);

        if (!$response->successful()) {
            Log::error('RedPill API error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'model' => $config['model'],
            ]);
            throw new \Exception('RedPill API error: ' . $response->body());
        }

        $data = $response->json();

        // Process the response based on the game type
        return $data['choices'][0]['message']['content'];
    }
}
