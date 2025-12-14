<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'subscription_tier',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    /**
     * @return HasMany<UserUsage, User>
     */
    public function usages(): HasMany
    {
        return $this->hasMany(UserUsage::class);
    }

    /**
     * Get usage stats for current billing period (month)
     *
     * @return array{cost: mixed, messages: mixed, tokens: mixed}
     */
    public function currentMonthUsage(): array
    {
        $stats = $this->usages()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->selectRaw('
                SUM(messages) as total_messages,
                SUM(tokens) as total_tokens,
                SUM(bytes) as total_bytes,
                SUM(cost) as total_cost')
            ->first();

        return [
            'messages' => $stats->total_messages ?? 0,
            'tokens' => $stats->total_tokens ?? 0,
            'bytes' => $stats->total_bytes ?? 0,
            'cost' => $stats->total_cost ?? 0,
        ];
    }

    public function hasExceededQuota(
        string $type = 'messages',
        int $limit = 100
    ): bool {
        $usage = $this->currentMonthUsage();

        return match ($type) {
            'messages' => $usage['messages'] >= $limit,
            'tokens' => $usage['tokens'] >= $limit,
            'bytes' => $usage['bytes'] >= $limit,
            default => false,
        };
    }
}
