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

    /**
     * Call the OpenAI API with image content
     */
    public function handleWithImages(array $config, string $systemPrompt, string $userPrompt, array $images): string
    {
        $client = Http::withToken(config('services.openai.key'))
            ->baseUrl('https://api.openai.com/v1');

        // Build message content as an array of text and image_url objects
        $messageContent = [
            ['type' => 'text', 'text' => $userPrompt]
        ];

        foreach ($images as $index => $imageUrl) {
            $messageContent[] = [
                'type' => 'image_url',
                'image_url' => [
                    'url' => $imageUrl,
                    'detail' => 'high'
                ]
            ];

            // Add a label for the image if provided
            //if (isset($images[$index]['label'])) {
            //    $messageContent[] = [
            //        'type' => 'text',
            //        'text' => $images[$index]['label']
            //    ];
            //}
        }

        $payload = [
            'model' => $config['model'] ?? 'gpt-4o',
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $messageContent]
            ],
            'max_tokens' => 1000,
            'temperature' => 0.7
        ];

        $response = $client->post('chat/completions', $payload);

        if (!$response->successful()) {
            Log::error('OpenAI API error with images', [
                'status' => $response->status(),
                'body' => $response->body(),
                'model' => $config['model'],
            ]);
            throw new \Exception('OpenAI API error with images: ' . $response->body());
        }

        $data = $response->json();

        return $data['choices'][0]['message']['content'];
    }
}
