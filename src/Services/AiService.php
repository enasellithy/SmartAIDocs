<?php

namespace SmartAIDocs\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiService
{
    protected $config;

    public function __construct()
    {
        $this->config = config('smart-ai-docs');
    }

    public function ask($prompt, $provider = null)
    {
        $config = config('smart-ai-docs');
        $provider = $provider ?: $config['default'];
        $settings = $config['providers'][$provider] ?? null;

        if (!$settings || empty($settings['api_key'])) {
            dump("⚠️ Configuration or API Key missing for provider: [$provider]");
            return null;
        }

        try {
            $url = rtrim($settings['base_url'], '/') . '/chat/completions';

            $response = \Illuminate\Support\Facades\Http::withToken($settings['api_key'])
                ->timeout(120)
                ->post($url, [
                    'model' => $settings['model'],
                    'messages' => [
                        ['role' => 'system', 'content' => "You are a Senior Laravel Developer. Output Markdown for docs and PHP for tests."],
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'temperature' => 0.3,
                ]);

            if ($response->successful()) {
                return $response->json()['choices'][0]['message']['content'];
            }

            dump("❌ AI Error ($provider): " . $response->body());
            return null;

        } catch (\Exception $e) {
            dump("🚨 Connection Error: " . $e->getMessage());
            return null;
        }
    }
}
