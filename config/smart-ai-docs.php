<?php

return [
    'default' => env('SMART_AI_PROVIDER', 'groq'),

    'providers' => [
        'ollama' => [
            'base_url' => env('OLLAMA_BASE_URL', 'http://localhost:11434'),
            'models' => [
                'docs' => env('OLLAMA_DOC_MODEL', 'deepseek-coder:6.7b'), 
                'tests' => env('OLLAMA_TEST_MODEL', 'qwen2.5-coder:1.5b'),
            ],
        ],
        'groq' => [
            'api_key'  => env('GROQ_API_KEY'),
            'base_url' => 'https://api.groq.com/openai/v1',
            'model'    => env('GROQ_MODEL', 'llama3-8b-8192'),
        ],
        'openai' => [
            'api_key'  => env('OPENAI_API_KEY'),
            'base_url' => 'https://api.openai.com/v1',
            'model'    => env('OPENAI_MODEL', 'gpt-4o'),
        ],
        'gemini' => [
            'api_key'  => env('GEMINI_API_KEY'),
            'base_url' => 'https://generativelanguage.googleapis.com/v1beta/openai/',
            'model'    => env('GEMINI_MODEL', 'gemini-1.5-flash'),
        ],
        'claude' => [
            'api_key'  => env('CLAUDE_API_KEY'),
            'base_url' => 'https://api.anthropic.com/v1',
            'model'    => env('CLAUDE_MODEL', 'claude-3-5-sonnet-20240620'),
        ],
    ],

    'output_paths' => [
        'docs'  => env('SMART_AI_DOCS_PATH', 'docs/ai-generated'),
        'tests' => env('SMART_AI_TESTS_PATH', 'tests/Unit/AI'),
    ]
];