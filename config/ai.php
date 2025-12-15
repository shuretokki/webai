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
            'id' => 'gemini-2.5-pro',
            'name' => 'Gemini 2.5 Pro',
            'provider' => 'gemini',
            'is_free' => false,
            'input_cost' => 0.0035,
            'output_cost' => 0.0105,
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
            'id' => 'gpt-4.5-preview',
            'name' => 'GPT-4.5 Preview',
            'provider' => 'openai',
            'is_free' => false,
            'input_cost' => 0.01,
            'output_cost' => 0.03,
        ],
        [
            'id' => 'gpt-4o',
            'name' => 'GPT-4o',
            'provider' => 'openai',
            'is_free' => false,
            'input_cost' => 0.005,
            'output_cost' => 0.015,
        ],
        [
            'id' => 'o1',
            'name' => 'o1',
            'provider' => 'openai',
            'is_free' => false,
            'input_cost' => 0.015,
            'output_cost' => 0.06,
        ],

        // Anthropic
        [
            'id' => 'claude-3-7-sonnet',
            'name' => 'Claude 3.7 Sonnet',
            'provider' => 'anthropic',
            'is_free' => false,
            'input_cost' => 0.003,
            'output_cost' => 0.015,
        ],
        [
            'id' => 'claude-3-5-sonnet',
            'name' => 'Claude 3.5 Sonnet',
            'provider' => 'anthropic',
            'is_free' => false,
            'input_cost' => 0.003,
            'output_cost' => 0.015,
        ],
        [
            'id' => 'claude-3-5-haiku',
            'name' => 'Claude 3.5 Haiku',
            'provider' => 'anthropic',
            'is_free' => false,
            'input_cost' => 0.00025,
            'output_cost' => 0.00125,
        ],

        // xAI
        [
            'id' => 'grok-2',
            'name' => 'Grok 2',
            'provider' => 'xai',
            'is_free' => false,
            'input_cost' => 0.005,
            'output_cost' => 0.015,
        ],

        // Mistral
        [
            'id' => 'mistral-large-2',
            'name' => 'Mistral Large 2',
            'provider' => 'mistral',
            'is_free' => false,
            'input_cost' => 0.002,
            'output_cost' => 0.006,
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
    ],
];
