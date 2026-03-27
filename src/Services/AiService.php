<?php

namespace SmartAIDocs\Services;

use Illuminate\Support\Facades\Http;

class AiService
{
    protected $config;

    public function __construct()
    {
        $this->config = config('smart-ai-docs');
    }

    public function ask($prompt, $provider = null, $taskType = 'docs')
    {
        $provider = $provider ?: ($this->config['default'] ?? 'ollama');
        $settings = $this->config['providers'][$provider] ?? null;

        if (!$settings) {
            return null;
        }

        $model = $settings['model'] ?? null;
        if ($provider === 'ollama' && isset($settings['models']) && is_array($settings['models'])) {
            $model = $settings['models'][$taskType] ?? $settings['models']['docs'];
        }

        try {
            $url = rtrim($settings['base_url'], '/');
            $endpoint = ($provider === 'ollama') ? "$url/api/generate" : "$url/chat/completions";

            $request = Http::timeout(150);

            if (!empty($settings['api_key'])) {
                $request->withToken($settings['api_key']);
            }

            $payload = $this->preparePayload($provider, $model, $prompt);
            $response = $request->post($endpoint, $payload);

            if ($response->successful()) {
                return ($provider === 'ollama') 
                    ? $response->json()['response'] 
                    : $response->json()['choices'][0]['message']['content'];
            }

            return null;

        } catch (\Exception $e) {
            return null;
        }
    }

    protected function preparePayload($provider, $model, $prompt)
    {
        $systemMessage = "You are a Senior Technical Writer and Laravel Expert. Your goal is to generate professional, Wiki-style documentation (GitBook style). Use clear headings, nested lists, and descriptive paragraphs. Avoid dry tables unless comparing data.";

        if ($provider === 'ollama') {
            return [
                'model' => $model,
                'prompt' => $prompt,
                'stream' => false,
                'system' => $systemMessage
            ];
        }

        return [
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => $systemMessage],
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.5,
        ];
    }
}