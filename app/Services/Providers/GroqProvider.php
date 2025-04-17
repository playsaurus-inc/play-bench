<?php

namespace App\Services\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GroqProvider implements AiProviderInterface
{
    /**
     * Call the Groq API
     */
    public function handle(array $config, string $systemPrompt, string $userPrompt): string
    {
        $response = Http::withToken(config('services.groq.key'))
            ->baseUrl('https://api.groq.com/openai/v1')
            ->post('chat/completions', [
            'model' => $config['model'],
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt]
            ],
            'temperature' => 0.7,
            'max_tokens' => 4096
        ]);

        if (!$response->successful()) {
            Log::error('Groq API error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'model' => $config['model'],
            ]);
            throw new \Exception('Groq API error: ' . $response->body());
        }

        $data = $response->json();
        $content = $data['choices'][0]['message']['content'];

        return $content;
    }

    /**
     * Call the AI API with images and return the response.
     *
     * @param array $config Configuration for the AI model
     * @param string $systemPrompt System prompt to be sent to the AI
     * @param string $userPrompt User content to be sent to the AI
     * @param array<int, string> $images Images to be sent to the AI. The images should be in base64 format.
     */
    public function handleWithImages(array $config, string $systemPrompt, string $userPrompt, array $images): string
    {
        throw new \Exception('Groq does not support images yet.');
    }
}
