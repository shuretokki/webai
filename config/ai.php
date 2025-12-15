<?php

return [
    'models' => [
        [
            'id' => 'gemini-2.5-flash',
            'name' => 'Gemini 2.5 Flash',
            'provider' => 'gemini',
            'is_free' => true,
            'input_cost' => 0.0001, // per 1k tokens
            'output_cost' => 0.0001,
        ],
        [
            'id' => 'gemini-2.5-flash-lite',
            'name' => 'Gemini 2.5 Flash Lite',
            'provider' => 'gemini',
            'is_free' => true,
            'input_cost' => 0.00005,
            'output_cost' => 0.00005,
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
            'id' => 'claude-3-5-sonnet',
            'name' => 'Claude 3.5 Sonnet',
            'provider' => 'anthropic',
            'is_free' => false,
            'input_cost' => 0.003,
            'output_cost' => 0.015,
        ],
    ],
];
