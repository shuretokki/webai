<?php

namespace App\Providers;

use App\Models\Attachment;
use App\Models\Chat;
use App\Observers\AttachmentObserver;
use App\Policies\ChatPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();
        Attachment::observe(AttachmentObserver::class);

        $this->configureRateLimiting();

        Gate::policy(Chat::class, ChatPolicy::class);

        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting Configuration
    |--------------------------------------------------------------------------
    |
    | Configure rate limiting for various application endpoints. All limits are
    | read from config/limits.php to ensure consistency across the application.
    |
    */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('chat-messages',
            fn (Request $request) => Limit::perMinute(config('limits.rate_limits.chat_messages'))
                ->by($request->user()->id)
                ->response(fn () => response()
                    ->json([
                        'error' => 'Too many messages. Please wait a moment.',
                    ], 429)));

        RateLimiter::for('api',
            fn (Request $request) => Limit::perMinute(config('limits.rate_limits.api'))
                ->by($request->ip()));

        RateLimiter::for('global',
            fn ($request) => Limit::perMinute(config('limits.rate_limits.global'))
                ->by($request->ip()));
    }
}
