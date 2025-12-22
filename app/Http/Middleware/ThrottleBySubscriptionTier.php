<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ThrottleBySubscriptionTier
{
    /**
     * Handle an incoming request and apply rate limits based on user subscription tier
     */
    public function handle(Request $request, Closure $next, string $limiter = 'api'): Response
    {
        $user = $request->user();

        if (! $user) {
            /**
             * Guest users get the lowest tier limits
             */
            return RateLimiter::attempt(
                "guest:{$limiter}:{$request->ip()}",
                perMinute: config('limits.subscription_tiers.free.rate_limit', 10),
                callback: fn () => $next($request),
            ) ?: abort(429, 'Too many requests. Please try again later.');
        }

        /**
         * Get user's subscription tier
         */
        $tier = $user->subscription_tier ?? 'free';

        /**
         * Get rate limit for tier and limiter type
         */
        $limits = match ($limiter) {
            'chat-messages' => [
                'free' => config('limits.subscription_tiers.free.chat_rate_limit', 2),
                'pro' => config('limits.subscription_tiers.pro.chat_rate_limit', 10),
                'enterprise' => config('limits.subscription_tiers.enterprise.chat_rate_limit', 50),
            ],
            'api' => [
                'free' => config('limits.subscription_tiers.free.api_rate_limit', 10),
                'pro' => config('limits.subscription_tiers.pro.api_rate_limit', 100),
                'enterprise' => config('limits.subscription_tiers.enterprise.api_rate_limit', 1000),
            ],
            default => [
                'free' => 10,
                'pro' => 100,
                'enterprise' => 1000,
            ],
        };

        $maxAttempts = $limits[$tier] ?? $limits['free'];

        /**
         * Apply rate limit
         */
        return RateLimiter::attempt(
            "{$tier}:{$limiter}:{$user->id}",
            perMinute: $maxAttempts,
            callback: fn () => $next($request),
        ) ?: abort(429, "Rate limit exceeded for {$tier} tier. Upgrade your plan for higher limits.");
    }
}

