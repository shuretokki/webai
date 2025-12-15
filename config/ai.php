<?php

return [
    'models' => [
        // Gemini
        [
            'id' => 'gemini-2.0-flash-exp',
            'name' => 'Gemini 2.0 Flash',
            'provider' => 'gemini',
            'is_free' => true,
            'input_cost' => 0.0001,
            'output_cost' => 0.0001,
        ],
        [
            'id' => 'gemini-1.5-pro',
            'name' => 'Gemini 1.5 Pro',
            'provider' => 'gemini',
            'is_free' => false,
            'input_cost' => 0.0035,
            'output_cost' => 0.0105,
        ],
        [
            'id' => 'gemini-1.5-flash',
            'name' => 'Gemini 1.5 Flash',
            'provider' => 'gemini',
            'is_free' => true,
            'input_cost' => 0.000075,
            'output_cost' => 0.0003,
        ],

        // OpenAI
        [
            'id' => 'gpt-4o',
            'name' => 'GPT-4o',
            'provider' => 'openai',
            'is_free' => false,
            'input_cost' => 0.005,
            'output_cost' => 0.015,
        ],
        [
            'id' => 'gpt-4o-mini',
            'name' => 'GPT-4o Mini',
            'provider' => 'openai',
            'is_free' => true,
            'input_cost' => 0.00015,
            'output_cost' => 0.0006,
        ],
        [
            'id' => 'o1-preview',
            'name' => 'o1 Preview',
            'provider' => 'openai',
            'is_free' => false,
            'input_cost' => 0.015,
            'output_cost' => 0.06,
        ],

        // Anthropic
        [
            'id' => 'claude-3-5-sonnet-20241022',
            'name' => 'Claude 3.5 Sonnet',
            'provider' => 'anthropic',
            'is_free' => false,
            'input_cost' => 0.003,
            'output_cost' => 0.015,
        ],
        [
            'id' => 'claude-3-opus-20240229',
            'name' => 'Claude 3 Opus',
            'provider' => 'anthropic',
            'is_free' => false,
            'input_cost' => 0.015,
            'output_cost' => 0.075,
        ],
        [
            'id' => 'claude-3-haiku-20240307',
            'name' => 'Claude 3 Haiku',
            'provider' => 'anthropic',
            'is_free' => true,
            'input_cost' => 0.00025,
            'output_cost' => 0.00125,
        ],

        // xAI
        [
            'id' => 'grok-beta',
            'name' => 'Grok Beta',
            'provider' => 'xai',
            'is_free' => false,
            'input_cost' => 0.005,
            'output_cost' => 0.015,
        ],

        // Mistral
        [
            'id' => 'mistral-large-latest',
            'name' => 'Mistral Large',
            'provider' => 'mistral',
            'is_free' => false,
            'input_cost' => 0.002,
            'output_cost' => 0.006,
        ],
        [
            'id' => 'mistral-small-latest',
            'name' => 'Mistral Small',
            'provider' => 'mistral',
            'is_free' => true,
            'input_cost' => 0.0002,
            'output_cost' => 0.0006,
        ],

        // Groq
        [
            'id' => 'llama3-70b-8192',
            'name' => 'Llama 3 70B (Groq)',
            'provider' => 'groq',
            'is_free' => true,
            'input_cost' => 0,
            'output_cost' => 0,
        ],
        [
            'id' => 'mixtral-8x7b-32768',
            'name' => 'Mixtral 8x7B (Groq)',
            'provider' => 'groq',
            'is_free' => true,
            'input_cost' => 0,
            'output_cost' => 0,
        ],

        // DeepSeek
        [
            'id' => 'deepseek-chat',
            'name' => 'DeepSeek V3',
            'provider' => 'deepseek',
            'is_free' => true,
            'input_cost' => 0.00014,
            'output_cost' => 0.00028,
        ],
        [
            'id' => 'deepseek-reasoner',
            'name' => 'DeepSeek R1',
            'provider' => 'deepseek',
            'is_free' => false,
            'input_cost' => 0.00055,
            'output_cost' => 0.00219,
        ],

        // Ollama (Local)
        [
            'id' => 'llama3',
            'name' => 'Llama 3 (Local)',
            'provider' => 'ollama',
            'is_free' => true,
            'input_cost' => 0,
            'output_cost' => 0,
        ],
    ],
];
