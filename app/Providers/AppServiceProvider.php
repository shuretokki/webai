<?php

namespace App\Providers;

use App\Models\Attachment;
use App\Observers\AttachmentObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\RateLimiter;
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
    }

    /**
     * Summary of configureRateLimiting
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('chat-messages',
            fn (Request $request) => Limit::perMinute(2)
                ->by($request->user()->id)
                ->response(fn () => response()
                    ->json([
                        'error' => 'Too many messages. Please wait a moment.',
                    ], 429)));

        RateLimiter::for('api',
            fn (Request $request) => Limit::perMinute(60)
                ->by($request->ip()));

        RateLimiter::for('global',
            fn ($request) => Limit::perMinute(100)
                ->by($request->ip()));
    }
}
