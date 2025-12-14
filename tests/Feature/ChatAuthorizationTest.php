<?php

use App\Models\Chat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('users cannot view other users chats', function () {
    $owner = User::factory()
        ->create();

    $intruder = User::factory()
        ->create();

    $chat = Chat::factory()
        ->create([
            'user_id' => $owner->id,
        ]);

    $this->actingAs($intruder)
        ->get("/chat/{$chat->id}")
        ->assertStatus(403);
});

test('users can view their own chats', function () {
    $user = User::factory()
        ->create();

    $chat = Chat::factory()
        ->create([
            'user_id' => $user->id,
        ]);

    $this->actingAs($user)
        ->get("/chat/{$chat->id}")
        ->assertStatus(200);
});

test('users cannot delete other users chats', function () {
    $owner = User::factory()
        ->create();

    $intruder = User::factory()
        ->create();

    $chat = Chat::factory()
        ->create([
            'user_id' => $owner->id,
        ]);

    $this->actingAs($intruder)
        ->delete("/chat/{$chat->id}")
        ->assertStatus(403);
});

test('users can delete their own chats', function () {
    $user = User::factory()
        ->create();

    $chat = Chat::factory()
        ->create([
            'user_id' => $user->id,
        ]);

    $this->actingAs($user)
        ->delete("/chat/{$chat->id}")
        ->assertStatus(302);

    expect($chat->fresh()->trashed())
        ->toBeTrue();
});
