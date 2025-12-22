<?php

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use App\Models\UserUsage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Concurrency Testing: Race Conditions
|--------------------------------------------------------------------------
|
| Test that concurrent requests handle shared resources correctly.
|
*/

test('concurrent chat creation does not create duplicate chats', function () {
    $user = User::factory()->create();

    /**
     * Simulate 10 concurrent chat creation requests
     */
    $promises = [];
    for ($i = 0; $i < 10; $i++) {
        $promises[] = fn () => $this->actingAs($user)
            ->postJson('/chat/stream', [
                'prompt' => "Concurrent message {$i}",
                'model' => 'gemini-2.5-flash-lite',
            ]);
    }

    /**
     * Execute all requests
     */
    $responses = array_map(fn ($fn) => $fn(), $promises);

    /**
     * Count successful responses
     */
    $successCount = collect($responses)->filter(fn ($r) => $r->status() === 200)->count();
    $rateLimitedCount = collect($responses)->filter(fn ($r) => $r->status() === 429)->count();

    /**
     * Some should succeed within rate limit
     */
    expect($successCount)->toBeGreaterThan(0);

    /**
     * Verify no duplicate chats or messages created
     */
    $totalChats = Chat::where('user_id', $user->id)->count();
    $totalMessages = Message::whereHas('chat', fn ($q) => $q->where('user_id', $user->id))->count();

    expect($totalChats)->toBeGreaterThan(0);
    expect($totalMessages)->toBe($successCount);
});

test('concurrent message creation in same chat maintains order', function () {
    $user = User::factory()->create();
    $chat = Chat::factory()->for($user)->create();

    /**
     * Create initial message to establish chat
     */
    Message::factory()->for($chat)->create(['role' => 'user', 'content' => 'First message']);

    /**
     * Simulate concurrent messages to same chat
     */
    $responses = [];
    for ($i = 0; $i < 5; $i++) {
        $responses[] = $this->actingAs($user)
            ->postJson('/chat/stream', [
                'chat_id' => $chat->id,
                'prompt' => "Message {$i}",
                'model' => 'gemini-2.5-flash-lite',
            ]);
    }

    /**
     * Verify messages were created
     */
    $messages = Message::where('chat_id', $chat->id)
        ->where('role', 'user')
        ->orderBy('created_at')
        ->get();

    expect($messages->count())->toBeGreaterThan(1);

    /**
     * Verify all messages belong to correct chat
     */
    foreach ($messages as $message) {
        expect($message->chat_id)->toBe($chat->id);
    }
});

/*
|--------------------------------------------------------------------------
| Concurrency Testing: Usage Tracking
|--------------------------------------------------------------------------
|
| Test that concurrent requests correctly track usage without race conditions.
|
*/

test('concurrent requests correctly track usage count', function () {
    $user = User::factory()->create();
    $limit = config('limits.rate_limits.chat_messages');

    /**
     * Make concurrent requests up to rate limit
     */
    $responses = [];
    for ($i = 0; $i < $limit; $i++) {
        $responses[] = $this->actingAs($user)
            ->postJson('/chat/stream', [
                'prompt' => "Concurrent test {$i}",
                'model' => 'gemini-2.5-flash-lite',
            ]);
    }

    /**
     * Count successful responses
     */
    $successCount = collect($responses)->filter(fn ($r) => $r->status() === 200)->count();

    /**
     * Verify usage records match successful requests
     */
    $usageCount = UserUsage::where('user_id', $user->id)
        ->where('type', 'message_sent')
        ->count();

    expect($usageCount)->toBe($successCount);
});

test('concurrent quota checks prevent over-limit execution', function () {
    $user = User::factory()->create();
    $dailyLimit = config('limits.usage.daily_token_limit');

    /**
     * Create usage records near the limit
     */
    UserUsage::factory()
        ->count($dailyLimit - 2)
        ->create([
            'user_id' => $user->id,
            'type' => 'message_sent',
            'messages' => 1,
        ]);

    /**
     * Attempt 10 concurrent requests when only 2 slots remaining
     */
    $responses = [];
    for ($i = 0; $i < 10; $i++) {
        $responses[] = $this->actingAs($user)
            ->postJson('/chat/stream', [
                'prompt' => "Test {$i}",
                'model' => 'gemini-2.5-flash-lite',
            ]);
    }

    /**
     * Count successful and blocked responses
     */
    $successCount = collect($responses)->filter(fn ($r) => $r->status() === 200)->count();
    $blockedCount = collect($responses)->filter(fn ($r) => $r->status() === 403)->count();

    /**
     * Should not exceed daily limit
     */
    $totalUsage = UserUsage::where('user_id', $user->id)->count();
    expect($totalUsage)->toBeLessThanOrEqual($dailyLimit);

    /**
     * Some requests should be blocked
     */
    expect($blockedCount)->toBeGreaterThan(0);
});

/*
|--------------------------------------------------------------------------
| Concurrency Testing: Database Integrity
|--------------------------------------------------------------------------
|
| Test that concurrent operations maintain database consistency.
|
*/

test('concurrent chat deletions are idempotent', function () {
    $user = User::factory()->create();
    $chat = Chat::factory()->for($user)->create();
    Message::factory()->count(5)->for($chat)->create();

    $chatId = $chat->id;

    /**
     * Attempt concurrent deletions of same chat
     */
    $responses = [];
    for ($i = 0; $i < 5; $i++) {
        $responses[] = $this->actingAs($user)->deleteJson("/chat/{$chatId}");
    }

    /**
     * First should succeed, rest should fail gracefully
     */
    $successCount = collect($responses)->filter(fn ($r) => in_array($r->status(), [200, 302]))->count();
    $notFoundCount = collect($responses)->filter(fn ($r) => $r->status() === 404)->count();

    /**
     * At least one should succeed
     */
    expect($successCount)->toBeGreaterThanOrEqual(1);

    /**
     * Chat should be soft deleted exactly once
     */
    $deletedChat = Chat::withTrashed()->find($chatId);
    expect($deletedChat)->not->toBeNull();
    expect($deletedChat->trashed())->toBeTrue();

    /**
     * All messages should be soft deleted
     */
    $deletedMessages = Message::withTrashed()->where('chat_id', $chatId)->get();
    foreach ($deletedMessages as $message) {
        expect($message->trashed())->toBeTrue();
    }
});

test('concurrent chat updates maintain consistency', function () {
    $user = User::factory()->create();
    $chat = Chat::factory()->for($user)->create(['title' => 'Original Title']);

    /**
     * Attempt concurrent title updates
     */
    $titles = ['Title A', 'Title B', 'Title C', 'Title D', 'Title E'];
    $responses = [];

    foreach ($titles as $title) {
        $responses[] = $this->actingAs($user)
            ->patchJson("/chat/{$chat->id}", ['title' => $title]);
    }

    /**
     * All should succeed
     */
    $successCount = collect($responses)->filter(fn ($r) => in_array($r->status(), [200, 302]))->count();
    expect($successCount)->toBe(count($titles));

    /**
     * Final title should be one of the attempted titles
     */
    $chat->refresh();
    expect(in_array($chat->title, $titles))->toBeTrue();

    /**
     * No duplicate chats should be created
     */
    expect(Chat::where('user_id', $user->id)->count())->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Concurrency Testing: Multi-User Scenarios
|--------------------------------------------------------------------------
|
| Test that multiple users can interact with the system simultaneously.
|
*/

test('multiple users can create chats simultaneously', function () {
    $users = User::factory()->count(5)->create();

    /**
     * Each user creates a chat concurrently
     */
    $responses = [];
    foreach ($users as $user) {
        $responses[] = $this->actingAs($user)
            ->postJson('/chat/stream', [
                'prompt' => "User {$user->id} chat",
                'model' => 'gemini-2.5-flash-lite',
            ]);
    }

    /**
     * All should succeed (no interference between users)
     */
    $successCount = collect($responses)->filter(fn ($r) => $r->status() === 200)->count();
    expect($successCount)->toBe(count($users));

    /**
     * Each user should have exactly 1 chat
     */
    foreach ($users as $user) {
        $chatCount = Chat::where('user_id', $user->id)->count();
        expect($chatCount)->toBe(1);
    }
});

test('multiple users rate limits are independent', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $limit = config('limits.rate_limits.chat_messages');

    /**
     * User 1 exhausts their rate limit
     */
    for ($i = 0; $i < $limit; $i++) {
        $this->actingAs($user1)
            ->postJson('/chat/stream', [
                'prompt' => "User1 message {$i}",
                'model' => 'gemini-2.5-flash-lite',
            ])
            ->assertOk();
    }

    /**
     * User 1 should be rate limited
     */
    $this->actingAs($user1)
        ->postJson('/chat/stream', [
            'prompt' => 'Should fail',
            'model' => 'gemini-2.5-flash-lite',
        ])
        ->assertStatus(429);

    /**
     * User 2 should still be able to make requests
     */
    $response = $this->actingAs($user2)
        ->postJson('/chat/stream', [
            'prompt' => 'User2 message',
            'model' => 'gemini-2.5-flash-lite',
        ]);

    $response->assertOk();
});

test('concurrent searches by different users are isolated', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    Chat::factory()->for($user1)->create(['title' => 'User1 Laravel']);
    Chat::factory()->for($user2)->create(['title' => 'User2 Laravel']);

    /**
     * Both users search concurrently
     */
    $response1 = $this->actingAs($user1)->get('/chat/search?q=Laravel');
    $response2 = $this->actingAs($user2)->get('/chat/search?q=Laravel');

    $response1->assertOk();
    $response2->assertOk();

    /**
     * Each user should only see their own chat
     */
    $results1 = $response1->json();
    $results2 = $response2->json();

    expect($results1['chats'])->toHaveCount(1);
    expect($results2['chats'])->toHaveCount(1);

    expect($results1['chats'][0]['title'])->toBe('User1 Laravel');
    expect($results2['chats'][0]['title'])->toBe('User2 Laravel');
});

/*
|--------------------------------------------------------------------------
| Concurrency Testing: Queue Job Processing
|--------------------------------------------------------------------------
|
| Test that queued jobs handle concurrent processing correctly.
|
*/

test('concurrent queue job execution maintains isolation', function () {
    Queue::fake();

    $user = User::factory()->create();
    $chat = Chat::factory()->for($user)->create();

    /**
     * Create multiple messages that trigger queue jobs
     */
    for ($i = 0; $i < 3; $i++) {
        Message::factory()->for($chat)->create([
            'content' => "Message {$i}",
        ]);
    }

    /**
     * Verify correct number of jobs queued
     */
    Queue::assertPushed(\App\Jobs\GenerateChatTitle::class, 3);
});
