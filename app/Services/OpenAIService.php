<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpenAIService
{
    private $apiKey;
    private $baseUrl = 'https://api.openai.com/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
    }

    public function generateStepSuggestions(string $goalTitle, string $goalDescription): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl, [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a goal-setting assistant. Break down goals into practical, actionable steps.'
                ],
                [
                    'role' => 'user',
                    'content' => "Please suggest 5-7 concrete steps to achieve this goal:\nTitle: {$goalTitle}\nDescription: {$goalDescription}\n\nFormat each step as a JSON object with 'title' and 'description' fields."
                ]
            ],
            'temperature' => 0.7,
            'max_tokens' => 1000,
        ]);

        if ($response->successful()) {
            $suggestions = $response->json()['choices'][0]['message']['content'];
            return json_decode($suggestions, true) ?: [];
        }

        return [];
    }
}