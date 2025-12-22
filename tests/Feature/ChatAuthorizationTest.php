<?php

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('users cannot view other users chats', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();

    $chat = Chat::factory()->create([
        'user_id' => $owner->id,
    ]);

    $response = $this->actingAs($intruder)
        ->get("/c/{$chat->id}");

    $response->assertForbidden();
    $response->assertDontSeeText($chat->title);

    // Verify database state unchanged
    expect($chat->fresh())->not->toBeNull();
    expect($chat->user_id)->toBe($owner->id);
});

test('guest cannot view any chats', function () {
    $chat = Chat::factory()->create();

    $this->get("/c/{$chat->id}")
        ->assertRedirect(route('login'));
});

test('users can view their own chats', function () {
    $user = User::factory()->create();

    $chat = Chat::factory()->create([
        'user_id' => $user->id,
        'title' => 'My Test Chat',
    ]);

    $response = $this->actingAs($user)
        ->get("/c/{$chat->id}");

    $response->assertOk();

    /**
     * Verify user can access their own chat
     */
    expect($chat->user_id)->toBe($user->id);
});

test('users can view their own chats with messages', function () {
    $user = User::factory()->create();
    $chat = Chat::factory()->create(['user_id' => $user->id]);

    $messages = Message::factory()->count(3)->create([
        'chat_id' => $chat->id,
    ]);

    $response = $this->actingAs($user)
        ->get("/c/{$chat->id}");

    $response->assertOk();

    /**
     * Verify messages exist in database
     */
    expect(Message::where('chat_id', $chat->id)->count())->toBe(3);
});

test('users cannot delete other users chats', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();

    $chat = Chat::factory()->create([
        'user_id' => $owner->id,
    ]);

    $response = $this->actingAs($intruder)
        ->delete("/c/{$chat->id}");

    $response->assertForbidden();

    // Verify chat still exists and unchanged
    $freshChat = $chat->fresh();
    expect($freshChat)->not->toBeNull();
    expect($freshChat->trashed())->toBeFalse();
    expect($freshChat->user_id)->toBe($owner->id);
});

test('guest cannot delete any chats', function () {
    $chat = Chat::factory()->create();

    $this->delete("/c/{$chat->id}")
        ->assertRedirect(route('login'));

    expect($chat->fresh())->not->toBeNull();
});

test('users can delete their own chats', function () {
    $user = User::factory()->create();

    $chat = Chat::factory()->create([
        'user_id' => $user->id,
    ]);

    /**
     * Create messages to test cascade
     */
    $messages = Message::factory()->count(5)->create([
        'chat_id' => $chat->id,
    ]);

    $response = $this->actingAs($user)
        ->delete("/c/{$chat->id}");

    $response->assertRedirect();

    /**
     * Verify soft delete
     */
    $freshChat = $chat->fresh();
    expect($freshChat)->not->toBeNull();
    expect($freshChat->trashed())->toBeTrue();
    expect($freshChat->user_id)->toBe($user->id);

    /**
     * Verify messages are also soft deleted
     */
    foreach ($messages as $message) {
        expect($message->fresh()->trashed())->toBeTrue();
    }
});

test('deleting non-existent chat returns 404', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->delete('/chat/99999')
        ->assertNotFound();
});
