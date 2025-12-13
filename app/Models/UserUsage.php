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
                $type, $tokens, $messages, $bytes),
            'metadata' => $metadata,
        ]);
    }

    protected static function calculateCost(
        string $type,
        int $tokens,
        int $messages,
        int $bytes
    ): float {
        return match ($type) {
            'ai_response' => $tokens * 0.0001,
            'file_upload' => $bytes * 0.00000001,
            default => 0,
        };
    }
}
