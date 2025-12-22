<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Rate Limits
    |--------------------------------------------------------------------------
    |
    | Configure rate limiting for various application endpoints. Values are
    | requests per minute unless otherwise specified. These limits are used
    | throughout the application and tests to ensure consistent behavior.
    |
    */

    'rate_limits' => [
        'chat_messages' => env('RATE_LIMIT_CHAT_MESSAGES', 2),
        'api' => env('RATE_LIMIT_API', 60),
        'global' => env('RATE_LIMIT_GLOBAL', 100),
    ],

    /*
    |--------------------------------------------------------------------------
    | File Upload Limits
    |--------------------------------------------------------------------------
    |
    | Configure maximum file sizes and allowed file types for uploads.
    | Sizes are in kilobytes (KB).
    |
    */

    'file_uploads' => [
        'max_size' => env('MAX_FILE_UPLOAD_SIZE', 10240), // 10MB in KB
        'avatar_max_size' => env('MAX_AVATAR_SIZE', 800), // 800KB
        'allowed_mimes' => [
            'images' => ['jpeg', 'jpg', 'png', 'gif'],
            'documents' => ['pdf', 'txt', 'doc', 'docx'],
            'all' => ['jpeg', 'jpg', 'png', 'gif', 'pdf', 'txt', 'doc', 'docx'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation Limits
    |--------------------------------------------------------------------------
    |
    | Configure maximum lengths and constraints for various input fields.
    |
    */

    'validation' => [
        'prompt_max_length' => env('PROMPT_MAX_LENGTH', 10000),
        'model_max_length' => env('MODEL_MAX_LENGTH', 100),
        'chat_title_max_length' => env('CHAT_TITLE_MAX_LENGTH', 500),
    ],

    /*
    |--------------------------------------------------------------------------
    | Search Limits
    |--------------------------------------------------------------------------
    |
    | Configure maximum results returned by search functionality.
    |
    */

    'search' => [
        'max_chats' => env('SEARCH_MAX_CHATS', 5),
        'max_messages' => env('SEARCH_MAX_MESSAGES', 10),
        'max_total_results' => env('SEARCH_MAX_TOTAL_RESULTS', 15),
    ],

    /*
    |--------------------------------------------------------------------------
    | Usage Tracking Limits
    |--------------------------------------------------------------------------
    |
    | Configure limits for AI usage tracking and quotas.
    |
    */

    'usage' => [
        'daily_token_limit' => env('DAILY_TOKEN_LIMIT', 100),
        'max_tokens_per_request' => env('MAX_TOKENS_PER_REQUEST', 50000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Subscription Tiers
    |--------------------------------------------------------------------------
    |
    | Configure limits and features for each subscription tier. This enables
    | tier-based rate limiting and feature access control.
    |
    */

    'subscription_tiers' => [
        'free' => [
            'daily_token_limit' => env('FREE_TIER_TOKENS', 100),
            'max_file_size' => env('FREE_TIER_FILE_SIZE', 2048), // 2MB
            'chat_rate_limit' => env('FREE_TIER_CHAT_RATE', 2), // per minute
            'api_rate_limit' => env('FREE_TIER_API_RATE', 10), // per minute
            'price' => 0,
        ],
        'pro' => [
            'daily_token_limit' => env('PRO_TIER_TOKENS', 10000),
            'max_file_size' => env('PRO_TIER_FILE_SIZE', 10240), // 10MB
            'chat_rate_limit' => env('PRO_TIER_CHAT_RATE', 10), // per minute
            'api_rate_limit' => env('PRO_TIER_API_RATE', 100), // per minute
            'price' => 2900, // $29.00/month in cents
        ],
        'enterprise' => [
            'daily_token_limit' => env('ENTERPRISE_TIER_TOKENS', 100000),
            'max_file_size' => env('ENTERPRISE_TIER_FILE_SIZE', 51200), // 50MB
            'chat_rate_limit' => env('ENTERPRISE_TIER_CHAT_RATE', 50), // per minute
            'api_rate_limit' => env('ENTERPRISE_TIER_API_RATE', 1000), // per minute
            'price' => 9900, // $99.00/month in cents
        ],
    ],

];
