<?php

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Performance Testing: Response Time
|--------------------------------------------------------------------------
|
| Ensure all endpoints respond within acceptable time limits (< 2 seconds).
|
*/

test('chat creation response time is under 2 seconds', function () {
    $user = User::factory()->create();

    $startTime = microtime(true);

    $response = $this->actingAs($user)
        ->postJson('/c/stream', [
            'prompt' => 'Test performance',
            'model' => 'gemini-2.5-flash-lite',
        ]);

    $duration = microtime(true) - $startTime;

    $response->assertOk();
    expect($duration)->toBeLessThan(2.0);
})->group('performance');

test('chat list page loads under 2 seconds', function () {
    $user = User::factory()->create();
    Chat::factory()->count(20)->for($user)->create();

    $startTime = microtime(true);

    $response = $this->actingAs($user)->get('/');

    $duration = microtime(true) - $startTime;

    $response->assertOk();
    expect($duration)->toBeLessThan(2.0);
})->group('performance');

test('search response time is under 2 seconds', function () {
    $user = User::factory()->create();
    Chat::factory()->count(50)->for($user)->create();

    $startTime = microtime(true);

    $response = $this->actingAs($user)->get('/chat/search?q=test');

    $duration = microtime(true) - $startTime;

    $response->assertOk();
    expect($duration)->toBeLessThan(2.0);
})->group('performance');

test('chat deletion response time is under 2 seconds', function () {
    $user = User::factory()->create();
    $chat = Chat::factory()->for($user)->create();
    Message::factory()->count(10)->for($chat)->create();

    $startTime = microtime(true);

    $response = $this->actingAs($user)->delete("/c/{$chat->id}");

    $duration = microtime(true) - $startTime;

    $response->assertRedirect();
    expect($duration)->toBeLessThan(2.0);
})->group('performance');

/*
|--------------------------------------------------------------------------
| Performance Testing: Database Query Count
|--------------------------------------------------------------------------
|
| Ensure N+1 query problems are prevented with eager loading.
|
*/

test('chat list avoids n plus 1 queries', function () {
    $user = User::factory()->create();
    Chat::factory()->count(20)->for($user)->create();

    DB::enableQueryLog();

    $this->actingAs($user)->get('/');

    $queries = DB::getQueryLog();
    $queryCount = count($queries);

    /**
     * Should be O(1) queries, not O(n) where n = number of chats
     */
    expect($queryCount)->toBeLessThan(10);
})->group('performance');

test('search results avoid n plus 1 queries', function () {
    $user = User::factory()->create();

    $chats = Chat::factory()->count(10)->for($user)->create();
    foreach ($chats as $chat) {
        Message::factory()->count(5)->for($chat)->create();
    }

    DB::enableQueryLog();

    $this->actingAs($user)->get('/chat/search?q=test');

    $queries = DB::getQueryLog();
    $queryCount = count($queries);

    /**
     * Should use eager loading, not individual queries per chat
     */
    expect($queryCount)->toBeLessThan(15);
})->group('performance');

/*
|--------------------------------------------------------------------------
| Performance Testing: Memory Usage
|--------------------------------------------------------------------------
|
| Ensure endpoints don't consume excessive memory.
|
*/

test('chat creation uses reasonable memory', function () {
    $user = User::factory()->create();

    $memoryBefore = memory_get_usage();

    $this->actingAs($user)
        ->postJson('/c/stream', [
            'prompt' => 'Memory test',
            'model' => 'gemini-2.5-flash-lite',
        ]);

    $memoryAfter = memory_get_usage();
    $memoryUsed = ($memoryAfter - $memoryBefore) / 1024 / 1024; // MB

    /**
     * Should use less than 10MB for single chat creation
     */
    expect($memoryUsed)->toBeLessThan(10);
})->group('performance');

test('large chat list uses reasonable memory', function () {
    $user = User::factory()->create();
    Chat::factory()->count(100)->for($user)->create();

    $memoryBefore = memory_get_usage();

    $this->actingAs($user)->get('/');

    $memoryAfter = memory_get_usage();
    $memoryUsed = ($memoryAfter - $memoryBefore) / 1024 / 1024; // MB

    /**
     * Should use less than 50MB for 100 chats
     */
    expect($memoryUsed)->toBeLessThan(50);
})->group('performance');

/*
|--------------------------------------------------------------------------
| Performance Testing: Bulk Operations
|--------------------------------------------------------------------------
|
| Ensure bulk operations scale efficiently.
|
*/

test('bulk chat deletion performs efficiently', function () {
    $user = User::factory()->create();
    $chats = Chat::factory()->count(50)->for($user)->create();

    $startTime = microtime(true);

    foreach ($chats->take(10) as $chat) {
        $this->actingAs($user)->delete("/c/{$chat->id}");
    }

    $duration = microtime(true) - $startTime;

    /**
     * 10 deletions should complete in under 5 seconds
     */
    expect($duration)->toBeLessThan(5.0);
})->group('performance');

test('multiple concurrent searches perform efficiently', function () {
    $user = User::factory()->create();
    Chat::factory()->count(30)->for($user)->create();

    $startTime = microtime(true);

    /**
     * Simulate 5 rapid searches
     */
    for ($i = 0; $i < 5; $i++) {
        $this->actingAs($user)->get("/chat/search?q=test{$i}");
    }

    $duration = microtime(true) - $startTime;

    /**
     * 5 searches should complete in under 3 seconds
     */
    expect($duration)->toBeLessThan(3.0);
})->group('performance');

/*
|--------------------------------------------------------------------------
| Performance Testing: Cache Efficiency
|--------------------------------------------------------------------------
|
| Test that frequently accessed data is cached appropriately.
|
*/

test('repeated chat access is fast due to caching', function () {
    $user = User::factory()->create();
    $chat = Chat::factory()->for($user)->create();

    /**
     * First access (cache miss)
     */
    $startTime1 = microtime(true);
    $this->actingAs($user)->get("/c/{$chat->id}");
    $duration1 = microtime(true) - $startTime1;

    /**
     * Second access (should be faster if cached)
     */
    $startTime2 = microtime(true);
    $this->actingAs($user)->get("/c/{$chat->id}");
    $duration2 = microtime(true) - $startTime2;

    /**
     * Both should be under 2 seconds
     */
    expect($duration1)->toBeLessThan(2.0);
    expect($duration2)->toBeLessThan(2.0);
})->group('performance');

/*
|--------------------------------------------------------------------------
| Performance Testing: Pagination Efficiency
|--------------------------------------------------------------------------
|
| Ensure pagination doesn't degrade with large datasets.
|
*/

test('paginated chat list performs consistently', function () {
    $user = User::factory()->create();
    Chat::factory()->count(200)->for($user)->create();

    /**
     * Test first page
     */
    $startTime1 = microtime(true);
    $this->actingAs($user)->get('/?page=1');
    $duration1 = microtime(true) - $startTime1;

    /**
     * Test middle page
     */
    $startTime2 = microtime(true);
    $this->actingAs($user)->get('/?page=5');
    $duration2 = microtime(true) - $startTime2;

    /**
     * Both pages should load in similar time (< 2s)
     */
    expect($duration1)->toBeLessThan(2.0);
    expect($duration2)->toBeLessThan(2.0);
    expect(abs($duration1 - $duration2))->toBeLessThan(0.5); // Within 500ms of each other
})->group('performance');
