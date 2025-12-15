<?php

use App\Http\Controllers\Api\UsageController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', fn () => Inertia::render('Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
]))->name('home');

Route::inertia('dashboard', 'Dashboard')
    ->name('dashboard')
    ->middleware(['auth', 'verified']);

Route::inertia('/settings/usage', 'settings/Usage')
    ->name('settings.usage')
    ->middleware(['auth', 'verified']);

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
            ->name('chat.search');

        Route::get('/chat/{chat}/export/{format?}', [ChatController::class, 'export'])
            ->name('chat.export')
            ->where('format', 'pdf|md');

        Route::get('/chat/{chat?}', [ChatController::class, 'index'])
            ->name('chat');
    });

require __DIR__.'/settings.php';
