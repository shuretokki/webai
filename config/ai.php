<?php

return [
    'models' => [
        // Gemini
        [
            'id' => 'gemini-3.0-pro',
            'name' => 'Gemini 3.0 Pro',
            'provider' => 'gemini',
            'is_free' => false,
            'input_cost' => 0.005,
            'output_cost' => 0.015,
        ],
        [
            'id' => 'gemini-2.5-flash',
            'name' => 'Gemini 2.5 Flash',
            'provider' => 'gemini',
            'is_free' => true,
            'input_cost' => 0.000075,
            'output_cost' => 0.0003,
        ],
        [
            'id' => 'gemini-2.5-flash-lite',
            'name' => 'Gemini 2.5 Flash Lite',
            'provider' => 'gemini',
            'is_free' => true,
            'input_cost' => 0.00005,
            'output_cost' => 0.0002,
        ],

        // OpenAI
        [
            'id' => 'gpt-5.2',
            'name' => 'GPT 5.2',
            'provider' => 'openai',
            'is_free' => false,
            'input_cost' => 0.02,
            'output_cost' => 0.06,
        ],

        // Anthropic
        [
            'id' => 'claude-4.5-sonnet',
            'name' => 'Claude 4.5 Sonnet',
            'provider' => 'anthropic',
            'is_free' => false,
            'input_cost' => 0.005,
            'output_cost' => 0.025,
        ],
        [
            'id' => 'claude-4.5-opus',
            'name' => 'Claude 4.5 Opus',
            'provider' => 'anthropic',
            'is_free' => false,
            'input_cost' => 0.03,
            'output_cost' => 0.15,
        ],

        // xAI
        [
            'id' => 'grok-4',
            'name' => 'Grok 4',
            'provider' => 'xai',
            'is_free' => false,
            'input_cost' => 0.01,
            'output_cost' => 0.03,
        ],

        // DeepSeek
        [
            'id' => 'deepseek-v3',
            'name' => 'DeepSeek V3',
            'provider' => 'deepseek',
            'is_free' => false,
            'input_cost' => 0.00014,
            'output_cost' => 0.00028,
        ],
        [
            'id' => 'deepseek-r1',
            'name' => 'DeepSeek R1',
            'provider' => 'deepseek',
            'is_free' => false,
            'input_cost' => 0.00055,
            'output_cost' => 0.00219,
        ],

        [
            'id' => 'llama-3.3-70b-versatile',
            'name' => 'Llama 3.3 70B',
            'provider' => 'groq',
            'is_free' => true,
            'input_cost' => 0.00,
            'output_cost' => 0.00,
        ],
        [
            'id' => 'llama-3.1-8b-instant',
            'name' => 'Llama 3.1 8B (Instant)',
            'provider' => 'groq',
            'is_free' => true,
            'input_cost' => 0.00,
            'output_cost' => 0.00,
        ],
        [
            'id' => 'mixtral-8x7b-32768',
            'name' => 'Mixtral 8x7B',
            'provider' => 'groq',
            'is_free' => true,
            'input_cost' => 0.00,
            'output_cost' => 0.00,
        ],
    ],
];
