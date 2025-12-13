<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', fn () => Inertia::render('Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
]))->name('home');

Route::inertia('dashboard', 'Dashboard')->name('dashboard')
    ->middleware(['auth', 'verified']);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::delete('/chat/{chat}', [ChatController::class, 'destroy'])->name('chat.destroy')
        ->middleware('can:delete, chat');
    Route::patch('/chat/{chat}', [ChatController::class, 'update'])->name('chat.update')
        ->middleware('can:update, chat');
    Route::post('/chat/stream', [ChatController::class, 'stream'])->name('chat.stream')
        ->middleware('throttle:chat-messages');
    Route::get('/chat', [ChatController::class, 'index'])->name('chat');
});

require __DIR__.'/settings.php';
