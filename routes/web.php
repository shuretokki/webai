<?php

use App\Http\Controllers\Api\UsageController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', fn () => Inertia::render('Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
]))->name('home');

Route::inertia('explore', 'Explore')
    ->name('explore')
    ->middleware(['auth', 'verified']);

Route::inertia('/settings/usage', 'settings/Usage')
    ->name('settings.usage')
    ->middleware(['auth', 'verified']);

Route::inertia('/pricing', 'Pricing')
    ->name('pricing');

Route::inertia('/enterprise', 'Enterprise')
    ->name('enterprise');

Route::inertia('/docs', 'Docs')
    ->name('docs');

Route::inertia('/blog', 'Blog')
    ->name('blog');

Route::inertia('/changelog', 'Changelog')
    ->name('changelog');

Route::inertia('/contact', 'Contact')
    ->name('contact');

Route::inertia('/community', 'Community')
    ->name('community');

Route::inertia('/about', 'About')
    ->name('about');

Route::inertia('/terms', 'Terms')
    ->name('terms');

Route::inertia('/privacy', 'Privacy')
    ->name('privacy');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/');
})->middleware('auth')->name('logout');

Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])
    ->name('social.redirect')
    ->where('provider', 'github|google');

Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])
    ->name('social.callback')
    ->where('provider', 'github|google');

Route::delete('/auth/{provider}/disconnect', [SocialAuthController::class, 'disconnect'])
    ->middleware('auth')
    ->name('social.disconnect')
    ->where('provider', 'github|google');

Route::middleware(['auth', 'verified'])
    ->prefix('api')
    ->group(function () {
        Route::get('/usage/current', [UsageController::class, 'current']);
    });

Route::middleware(['auth', 'verified'])
    ->group(function () {
        Route::delete('/chat/{chat}', [ChatController::class, 'destroy'])
            ->name('chat.destroy')
            ->can('delete', 'chat');

        Route::patch('/chat/{chat}', [ChatController::class, 'update'])
            ->name('chat.update')
            ->can('update', 'chat');

        Route::post('/chat/stream', [ChatController::class, 'stream'])
            ->name('chat.stream')
            ->middleware('throttle:chat-messages');

        Route::get('/chat/search', [ChatController::class, 'search'])
            ->name('chat.search')
            ->middleware('throttle:60,1');

        Route::get('/chat/{chat}/export/{format?}', [ChatController::class, 'export'])
            ->name('chat.export')
            ->can('view', 'chat')
            ->where('format', 'pdf|md');

        Route::get('/chat/{chat?}', [ChatController::class, 'index'])
            ->name('chat');
    });

require __DIR__.'/settings.php';

Route::fallback(function () {
    return Inertia::render('NotFound');
});
