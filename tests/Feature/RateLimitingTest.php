<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Test that chat stream endpoint enforces rate limiting.
 *
 * Verifies that after 2 successful requests within a minute,
 * the 3rd request is blocked with HTTP 429 status and
 * appropriate error message.
 */
test('chat stream is blocked after 2 requests per minute', function () {
    $user = User::factory()->create();

    /* First request should succeed */
    $this->actingAs($user)
        ->postJson('/chat/stream', [
            'prompt' => 'Test 1',
            'model' => 'gemini-2.0-flash-lite',
        ])
        ->assertOk();

    /* Second request should succeed */
    $this->actingAs($user)
        ->postJson('/chat/stream', [
            'prompt' => 'Test 2',
            'model' => 'gemini-2.0-flash-lite',
        ])
        ->assertOk();

    /* Third request should be rate limited */
    $this->actingAs($user)
        ->postJson('/chat/stream', [
            'prompt' => 'Test 3',
            'model' => 'gemini-2.0-flash-lite',
        ])
        ->assertStatus(429)
        ->assertJson([
            'error' => 'Too many messages. Please wait a moment.',
        ]);
});

/**
 * Test that rate limits are tracked independently per user.
 *
 * Verifies that each user has their own rate limit counter,
 * so User 1 hitting the limit does not affect User 2's ability
 * to send messages.
 */
test('different users have independent rate limits', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    /* User 1 sends first message */
    $this->actingAs($user1)
        ->postJson('/chat/stream', [
            'prompt' => 'User 1 Test 1',
            'model' => 'gemini-2.0-flash-lite',
        ])
        ->assertOk();

    /* User 1 sends second message */
    $this->actingAs($user1)
        ->postJson('/chat/stream', [
            'prompt' => 'User 1 Test 2',
            'model' => 'gemini-2.0-flash-lite',
        ])
        ->assertOk();

    /* User 2 can still send messages (independent limit) */
    $this->actingAs($user2)
        ->postJson('/chat/stream', [
            'prompt' => 'User 2 Test 1',
            'model' => 'gemini-2.0-flash-lite',
        ])
        ->assertOk();

    /* User 2's second message also succeeds */
    $this->actingAs($user2)
        ->postJson('/chat/stream', [
            'prompt' => 'User 2 Test 2',
            'model' => 'gemini-2.0-flash-lite',
        ])
        ->assertOk();
});
