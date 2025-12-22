<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->chatRateLimit = config('limits.rate_limits.chat_messages');
});
test('chat stream enforces configured rate limit dynamically', function () {
    $user = User::factory()->create();
    $limit = $this->chatRateLimit;

    /**
     * Make exactly $limit requests - all should succeed
     */
    for ($i = 1; $i <= $limit; $i++) {
        $response = $this->actingAs($user)
            ->postJson('/c/stream', [
                'prompt' => "Test request {$i} of {$limit}",
                'model' => 'gemini-2.5-flash-lite',
            ]);

        $response->assertOk();

        /**
         * Verify rate limit header shows correct limit
         */
        expect($response->headers->has('X-RateLimit-Limit'))->toBeTrue();
        $headerLimit = (int) $response->headers->get('X-RateLimit-Limit');
        expect($headerLimit)->toBe($limit, "Rate limit header should match configured limit");

        /**
         * Verify remaining count decreases correctly
         */
        $remaining = (int) $response->headers->get('X-RateLimit-Remaining');
        expect($remaining)->toBe($limit - $i, "Request {$i}: remaining should be " . ($limit - $i));
    }

    /**
     * The next request (limit + 1) should be rate limited
     */
    $blockedResponse = $this->actingAs($user)
        ->postJson('/c/stream', [
            'prompt' => 'This should be blocked',
            'model' => 'gemini-2.5-flash-lite',
        ]);

    $blockedResponse->assertStatus(429);
    $blockedResponse->assertJson([
        'error' => 'Too many messages. Please wait a moment.',
    ]);
    $blockedResponse->assertJsonStructure(['error']);

    /**
     * Verify rate limit headers on blocked request
     */
    expect($blockedResponse->headers->has('X-RateLimit-Limit'))->toBeTrue();
    expect($blockedResponse->headers->get('X-RateLimit-Remaining'))->toBe('0');
});

test('rate limit resets after time window', function () {
    $user = User::factory()->create();
    $limit = $this->chatRateLimit;

    /**
     * Exhaust the rate limit
     */
    for ($i = 1; $i <= $limit; $i++) {
        $this->actingAs($user)->postJson('/c/stream', [
            'prompt' => "Test {$i}",
            'model' => 'gemini-2.5-flash-lite',
        ])->assertOk();
    }

    /**
     * Next request should fail
     */
    $this->actingAs($user)->postJson('/c/stream', [
        'prompt' => 'Should fail',
        'model' => 'gemini-2.5-flash-lite',
    ])->assertStatus(429);

    /**
     * Travel forward 61 seconds (past the 1-minute window)
     */
    $this->travel(61)->seconds();

    /**
     * Rate limit should be reset
     */
    $this->actingAs($user)->postJson('/c/stream', [
        'prompt' => 'Should work after reset',
        'model' => 'gemini-2.5-flash-lite',
    ])->assertOk();
});

test('different users have independent rate limits', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $limit = $this->chatRateLimit;

    /**
     * User 1 exhausts their limit
     */
    for ($i = 1; $i <= $limit; $i++) {
        $this->actingAs($user1)
            ->postJson('/c/stream', [
                'prompt' => "User 1 Test {$i}",
                'model' => 'gemini-2.5-flash-lite',
            ])
            ->assertOk();
    }

    /**
     * User 1 is now blocked
     */
    $this->actingAs($user1)
        ->postJson('/c/stream', [
            'prompt' => 'User 1 blocked',
            'model' => 'gemini-2.5-flash-lite',
        ])
        ->assertStatus(429);

    /**
     * User 2 should still have full quota available
     */
    for ($i = 1; $i <= $limit; $i++) {
        $response = $this->actingAs($user2)
            ->postJson('/c/stream', [
                'prompt' => "User 2 Test {$i}",
                'model' => 'gemini-2.5-flash-lite',
            ]);

        $response->assertOk();

        /**
         * Verify User 2 has independent counter
         */
        $remaining = (int) $response->headers->get('X-RateLimit-Remaining');
        expect($remaining)->toBe($limit - $i);
    }
});
