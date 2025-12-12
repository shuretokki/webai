<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');
Route::inertia('dashboard', 'Dashboard')->middleware(['auth', 'verified'])->name('dashboard');
Route::middleware(['auth', 'verified'])->group(function () {
    Route::delete('/chat/{chat}', [ChatController::class, 'destroy'])->name('chat.destroy');
    Route::patch('/chat/{chat}', [ChatController::class, 'update'])->name('chat.update');
    Route::post('/chat/stream', [ChatController::class, 'stream'])->name('chat.stream');
    Route::get('/chat', [ChatController::class, 'index'])->name('chat');
});

require __DIR__.'/settings.php';
