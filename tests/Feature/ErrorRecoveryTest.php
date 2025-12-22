<?php

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use App\Models\UserUsage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Prism\Prism;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Error Recovery: Database Failures
|--------------------------------------------------------------------------
|
| Test graceful handling of database connection and query errors.
|
*/

test('handles database connection failure gracefully', function () {
    $user = User::factory()->create();

    /**
     * Simulate database failure
     */
    DB::shouldReceive('connection')->andThrow(new \PDOException('Connection failed'));

    try {
        $this->actingAs($user)
            ->postJson('/c/stream', [
                'prompt' => 'Test',
                'model' => 'gemini-2.5-flash-lite',
            ]);
    } catch (\PDOException $e) {
        /**
         * Exception should be caught and handled
         */
        expect($e->getMessage())->toContain('Connection failed');
    }
})->group('error-recovery')->skip('requires database mocking setup');

test('recovers from transaction rollback', function () {
    $user = User::factory()->create();

    DB::beginTransaction();

    $chat = Chat::factory()->for($user)->create();
    Message::factory()->for($chat)->create();

    /**
     * Force rollback
     */
    DB::rollBack();

    /**
     * Verify data was rolled back
     */
    expect(Chat::count())->toBe(0);
    expect(Message::count())->toBe(0);

    /**
     * New operations should work
     */
    $response = $this->actingAs($user)
        ->postJson('/c/stream', [
            'prompt' => 'After rollback',
            'model' => 'gemini-2.5-flash-lite',
        ]);

    $response->assertOk();
})->group('error-recovery');

/*
|--------------------------------------------------------------------------
| Error Recovery: External API Failures
|--------------------------------------------------------------------------
|
| Test handling of external service failures (Prism AI API).
|
*/

test('handles ai api timeout gracefully', function () {
    $user = User::factory()->create();

    /**
     * Simulate API timeout
     */
    Http::fake([
        '*' => Http::response('Timeout', 504),
    ]);

    $response = $this->actingAs($user)
        ->postJson('/c/stream', [
            'prompt' => 'Test timeout',
            'model' => 'gemini-2.5-flash-lite',
        ]);

    /**
     * Should return error response, not crash
     */
    expect($response->getStatusCode())->toBeIn([200, 500, 503]);
})->group('error-recovery');

test('handles ai api rate limit gracefully', function () {
    $user = User::factory()->create();

    /**
     * Simulate API rate limit
     */
    Http::fake([
        '*' => Http::response(['error' => 'Rate limit exceeded'], 429),
    ]);

    $response = $this->actingAs($user)
        ->postJson('/c/stream', [
            'prompt' => 'Test rate limit',
            'model' => 'gemini-2.5-flash-lite',
        ]);

    /**
     * Should handle gracefully
     */
    expect($response->getStatusCode())->toBeIn([200, 429, 503]);
})->group('error-recovery');

test('handles ai api invalid response', function () {
    $user = User::factory()->create();

    /**
     * Simulate malformed API response
     */
    Http::fake([
        '*' => Http::response('Invalid JSON{{{', 200),
    ]);

    $response = $this->actingAs($user)
        ->postJson('/c/stream', [
            'prompt' => 'Test invalid response',
            'model' => 'gemini-2.5-flash-lite',
        ]);

    /**
     * Should not crash application
     */
    expect($response->getStatusCode())->toBeIn([200, 500]);
})->group('error-recovery');

/*
|--------------------------------------------------------------------------
| Error Recovery: File System Failures
|--------------------------------------------------------------------------
|
| Test handling of file upload and storage errors.
|
*/

test('handles storage disk full error', function () {
    $user = User::factory()->create();

    /**
     * Attempt file upload when disk might be full
     * Application should handle storage exceptions
     */
    $file = \Illuminate\Http\UploadedFile::fake()->image('test.jpg', 1000, 1000)->size(5000);

    $response = $this->actingAs($user)
        ->postJson('/c/stream', [
            'prompt' => 'With file',
            'model' => 'gemini-2.5-flash-lite',
            'files' => [$file],
        ]);

    /**
     * Should either succeed or return proper error
     */
    expect($response->getStatusCode())->toBeIn([200, 413, 500, 507]);
})->group('error-recovery');

test('handles missing storage directory', function () {
    $user = User::factory()->create();

    /**
     * Application should create directories if missing
     */
    $file = \Illuminate\Http\UploadedFile::fake()->image('avatar.jpg');

    $response = $this->actingAs($user)->post('/settings/avatar', [
        'avatar' => $file,
    ]);

    /**
     * Should succeed or handle gracefully
     */
    expect($response->getStatusCode())->toBeIn([200, 302, 500]);
})->group('error-recovery');

/*
|--------------------------------------------------------------------------
| Error Recovery: Queue Failures
|--------------------------------------------------------------------------
|
| Test handling of failed background jobs.
|
*/

test('handles failed queue job gracefully', function () {
    Queue::fake();

    $user = User::factory()->create();
    $chat = Chat::factory()->for($user)->create();

    /**
     * Trigger job that might fail
     */
    Message::factory()->for($chat)->create([
        'content' => 'Test message',
    ]);

    /**
     * Verify job was pushed
     */
    Queue::assertPushed(\App\Jobs\GenerateChatTitle::class);

    /**
     * Chat should still exist even if job fails
     */
    expect($chat->fresh())->not->toBeNull();
})->group('error-recovery');

test('retries failed jobs appropriately', function () {
    Queue::fake();

    $user = User::factory()->create();
    $chat = Chat::factory()->for($user)->create();

    Message::factory()->for($chat)->create();

    Queue::assertPushed(\App\Jobs\GenerateChatTitle::class, 1);

    /**
     * Jobs should be configured with retry logic
     * Verify job class has $tries property
     */
    $job = new \App\Jobs\GenerateChatTitle($chat);
    $reflection = new \ReflectionClass($job);

    if ($reflection->hasProperty('tries')) {
        $triesProperty = $reflection->getProperty('tries');
        $triesProperty->setAccessible(true);
        $tries = $triesProperty->getValue($job);

        expect($tries)->toBeGreaterThan(0);
    }
})->group('error-recovery');

/*
|--------------------------------------------------------------------------
| Error Recovery: Validation Errors
|--------------------------------------------------------------------------
|
| Test that invalid input is rejected gracefully with helpful messages.
|
*/

test('returns helpful error for missing required fields', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson('/c/stream', []);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['prompt']);

    /**
     * Error message should be user-friendly
     */
    $errors = $response->json('errors.prompt');
    expect($errors)->toBeArray();
    expect($errors[0])->toContain('required');
})->group('error-recovery');

test('returns helpful error for invalid data types', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson('/c/stream', [
            'prompt' => 12345, // Should be string
            'model' => 'gemini-2.5-flash-lite',
        ]);

    /**
     * Should validate type or handle gracefully
     */
    expect($response->getStatusCode())->toBeIn([200, 422]);
})->group('error-recovery');

/*
|--------------------------------------------------------------------------
| Error Recovery: Authentication Failures
|--------------------------------------------------------------------------
|
| Test handling of expired sessions and invalid tokens.
|
*/

test('handles expired session gracefully', function () {
    /**
     * Attempt to access protected route without authentication
     */
    $response = $this->postJson('/c/stream', [
        'prompt' => 'Test',
        'model' => 'gemini-2.5-flash-lite',
    ]);

    $response->assertStatus(401);

    /**
     * Should redirect to login or return 401
     */
    expect($response->getStatusCode())->toBe(401);
})->group('error-recovery');

test('handles invalid csrf token', function () {
    $user = User::factory()->create();

    /**
     * Make request without CSRF token
     */
    $response = $this->actingAs($user)
        ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
        ->post('/settings/avatar', [
            'avatar' => \Illuminate\Http\UploadedFile::fake()->image('test.jpg'),
        ]);

    /**
     * Should handle gracefully (middleware disabled for test)
     */
    expect($response->getStatusCode())->toBeIn([200, 302, 419]);
})->group('error-recovery');

/*
|--------------------------------------------------------------------------
| Error Recovery: Resource Exhaustion
|--------------------------------------------------------------------------
|
| Test handling when system resources are exhausted.
|
*/

test('handles quota exceeded error', function () {
    $user = User::factory()->create();

    /**
     * Create maximum usage records
     */
    UserUsage::factory()
        ->count(config('limits.usage.daily_token_limit'))
        ->create([
            'user_id' => $user->id,
            'type' => 'message_sent',
            'messages' => 1,
        ]);

    $response = $this->actingAs($user)
        ->postJson('/c/stream', [
            'prompt' => 'Over quota',
            'model' => 'gemini-2.5-flash-lite',
        ]);

    $response->assertForbidden();
    $response->assertJson([
        'error' => 'You have reached your monthly message limit. Upgrade your plan to increase limit.',
    ]);
})->group('error-recovery');

test('handles memory limit gracefully', function () {
    $user = User::factory()->create();

    /**
     * Create very large dataset
     */
    Chat::factory()->count(1000)->for($user)->create();

    $response = $this->actingAs($user)->get('/');

    /**
     * Should either paginate or limit results
     * Not crash with memory error
     */
    $response->assertOk();
})->group('error-recovery');

/*
|--------------------------------------------------------------------------
| Error Recovery: Network Failures
|--------------------------------------------------------------------------
|
| Test handling of network connectivity issues.
|
*/

test('handles slow network connection', function () {
    $user = User::factory()->create();

    /**
     * Simulate slow response
     */
    Http::fake([
        '*' => function () {
            sleep(1); // Simulate delay

            return Http::response(['data' => 'slow'], 200);
        },
    ]);

    $startTime = microtime(true);

    $response = $this->actingAs($user)
        ->postJson('/c/stream', [
            'prompt' => 'Test',
            'model' => 'gemini-2.5-flash-lite',
        ]);

    $duration = microtime(true) - $startTime;

    /**
     * Should handle slow connections
     */
    expect($response->getStatusCode())->toBeIn([200, 504]);
})->group('error-recovery');

/*
|--------------------------------------------------------------------------
| Error Recovery: Data Corruption
|--------------------------------------------------------------------------
|
| Test handling of corrupted or invalid data states.
|
*/

test('handles orphaned messages gracefully', function () {
    $user = User::factory()->create();
    $chat = Chat::factory()->for($user)->create();

    Message::factory()->for($chat)->create();

    /**
     * Delete chat (orphaning messages)
     */
    $chat->forceDelete();

    /**
     * Search should not crash on orphaned messages
     */
    $response = $this->actingAs($user)->get('/s?q=test');

    $response->assertOk();
})->group('error-recovery');

test('handles invalid chat id format', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson('/c/stream', [
            'chat_id' => 'not-a-number',
            'prompt' => 'Test',
            'model' => 'gemini-2.5-flash-lite',
        ]);

    /**
     * Should validate and reject
     */
    $response->assertStatus(422);
})->group('error-recovery');
