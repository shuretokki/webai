<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class AddRateLimitHeaders
{
    /**
     * Add rate limit headers to response
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        /**
         * Get rate limit key from request
         */
        $key = $this->resolveRequestSignature($request);

        /**
         * Get remaining attempts
         */
        if ($request->route()) {
            $middleware = collect($request->route()->middleware());
            $throttle = $middleware->first(fn ($m) => str_starts_with($m, 'throttle'));

            if ($throttle) {
                /**
                 * Parse throttle parameters (e.g., "throttle:60,1" or "throttle:chat-messages")
                 */
                $parts = explode(':', $throttle);
                $params = isset($parts[1]) ? explode(',', $parts[1]) : [];

                if (count($params) >= 1) {
                    $maxAttempts = is_numeric($params[0]) ? (int) $params[0] : config("limits.rate_limits.{$params[0]}", 60);
                    $remaining = RateLimiter::remaining($key, $maxAttempts);
                    $retryAfter = RateLimiter::availableIn($key);

                    /**
                     * Add headers
                     */
                    $response->headers->set('X-RateLimit-Limit', $maxAttempts);
                    $response->headers->set('X-RateLimit-Remaining', max(0, $remaining));

                    if ($remaining <= 0) {
                        $response->headers->set('Retry-After', $retryAfter);
                        $response->headers->set('X-RateLimit-Reset', now()->addSeconds($retryAfter)->timestamp);
                    }
                }
            }
        }

        return $response;
    }

    /**
     * Resolve request signature for rate limiting
     */
    protected function resolveRequestSignature(Request $request): string
    {
        if ($user = $request->user()) {
            return sha1($user->getAuthIdentifier());
        }

        return sha1($request->ip());
    }
}
