<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\ChatController;


Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/test', [ChatController::class, 'test']);
Route::post('/chat/send', [ChatController::class, 'chat']);
Route::get('/chat', function () {
    return Inertia::render('chat/Index');
})->name('chat');

require __DIR__.'/settings.php';
