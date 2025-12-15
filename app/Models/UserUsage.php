<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserUsage extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'metadata' => 'array',
        'cost' => 'decimal:4',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Record a usage event
     *
     * @return void
     */
    public static function record(
        int $userId,
        string $type,
        int $tokens = 0,
        int $messages = 0,
        int $bytes = 0,
        array $metadata = []
    ): self {
        return self::create([
            'user_id' => $userId,
            'type' => $type,
            'tokens' => $tokens,
            'messages' => $messages,
            'bytes' => $bytes,
            'cost' => self::calculateCost(
                $type, $tokens, $messages, $bytes, $metadata),
            'metadata' => $metadata,
        ]);
    }

    protected static function calculateCost(
        string $type,
        int $tokens,
        int $messages,
        int $bytes,
        array $metadata = []
    ): float {
        if ($type === 'ai_response') {
            $modelId = $metadata['model'] ?? 'gemini-2.5-flash';
            $models = config('ai.models', []);
            $modelConfig = collect($models)->firstWhere('id', $modelId);

            if (!$modelConfig) {
                return $tokens * 0.0001; // Fallback
            }

            // Simplified calculation (assuming tokens are total, or split 50/50 if not specified)
            // Ideally we should track input/output tokens separately.
            // For now, let's just use input_cost as a base or average.
            // Or better, check if metadata has input_tokens and output_tokens

            $inputTokens = $metadata['input_tokens'] ?? $tokens;
            $outputTokens = $metadata['output_tokens'] ?? 0;

            $cost = ($inputTokens / 1000) * $modelConfig['input_cost']
                  + ($outputTokens / 1000) * $modelConfig['output_cost'];

            return $cost;
        }

        return match ($type) {
            'file_upload' => $bytes * 0.00000001,
            default => 0,
        };
    }
}
