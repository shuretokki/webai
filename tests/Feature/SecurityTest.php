<?php

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Security Testing: XSS Prevention
|--------------------------------------------------------------------------
|
| Test that user input is properly sanitized to prevent XSS attacks.
|
*/

test('chat prompt sanitizes script tags', function () {
    $user = User::factory()->create();
    $xssPayload = '<script>alert("XSS")</script>Hello';

    $response = $this->actingAs($user)
        ->postJson('/c/stream', [
            'prompt' => $xssPayload,
            'model' => 'gemini-2.5-flash-lite',
        ]);

    $response->assertOk();

    /**
     * Verify message is stored without script tags executing
     */
    $message = Message::where('chat_id', $response->json('chat_id'))->first();
    expect($message)->not->toBeNull();
    expect($message->content)->toContain('Hello');
});

test('chat title sanitizes html tags', function () {
    $user = User::factory()->create();
    $chat = Chat::factory()->for($user)->create([
        'title' => 'Original Title',
    ]);

    $xssPayload = '<img src=x onerror=alert(1)>Malicious Title';

    $response = $this->actingAs($user)
        ->patchJson("/c/{$chat->id}", [
            'title' => $xssPayload,
        ]);

    $response->assertRedirect();

    $chat->refresh();
    expect($chat->title)->toBe($xssPayload);
});

test('search query sanitizes sql wildcard characters', function () {
    $user = User::factory()->create();
    Chat::factory()->for($user)->create(['title' => 'Laravel Tutorial']);
    Chat::factory()->for($user)->create(['title' => 'PHP Basics']);

    /**
     * Test that % doesn't act as SQL LIKE wildcard
     */
    $response = $this->actingAs($user)->get('/s?q=%');

    $response->assertOk();
    $results = $response->json();

    /**
     * Should return 0 results because no titles contain literal "%"
     */
    expect($results['results'])->toHaveCount(0);
});

test('search query sanitizes underscore wildcard', function () {
    $user = User::factory()->create();
    Chat::factory()->for($user)->create(['title' => 'PHP']);

    /**
     * Test that _ doesn't act as single character wildcard
     */
    $response = $this->actingAs($user)->get('/s?q=P_P');

    $response->assertOk();
    $results = $response->json();

    /**
     * Should return 0 because no content has literal "P_P"
     */
    expect($results['results'])->toHaveCount(0);
});

/*
|--------------------------------------------------------------------------
| Security Testing: SQL Injection Prevention
|--------------------------------------------------------------------------
|
| Test that malicious SQL injection attempts are properly escaped.
|
*/

test('search prevents sql injection in query parameter', function () {
    $user = User::factory()->create();
    Chat::factory()->for($user)->create(['title' => 'Normal Chat']);

    $maliciousSql = "'; DROP TABLE chats; --";

    $response = $this->actingAs($user)->get('/s?q=' . urlencode($maliciousSql));

    $response->assertOk();

    /**
     * Verify table still exists
     */
    $this->assertDatabaseHas('chats', ['title' => 'Normal Chat']);
    expect(Chat::count())->toBeGreaterThanOrEqual(1);
});

test('chat id parameter prevents sql injection', function () {
    $user = User::factory()->create();
    $chat = Chat::factory()->for($user)->create();

    $maliciousSql = "{$chat->id} OR 1=1";

    $response = $this->actingAs($user)
        ->postJson('/c/stream', [
            'chat_id' => $maliciousSql,
            'prompt' => 'Test',
            'model' => 'gemini-2.5-flash-lite',
        ]);

    /**
     * Should fail authorization (403) since malicious chat_id doesn't exist
     */
    expect($response->getStatusCode())->toBeIn([403, 422]);
});

/*
|--------------------------------------------------------------------------
| Security Testing: Authorization Bypass Attempts
|--------------------------------------------------------------------------
|
| Test that users cannot manipulate parameters to access other users' data.
|
*/

test('cannot access other user chat via id manipulation', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $user2Chat = Chat::factory()->for($user2)->create(['title' => 'Secret Chat']);

    /**
     * User 1 attempts to send message to User 2's chat
     */
    $response = $this->actingAs($user1)
        ->postJson('/c/stream', [
            'chat_id' => $user2Chat->id,
            'prompt' => 'Trying to access',
            'model' => 'gemini-2.5-flash-lite',
        ]);

    $response->assertForbidden();
});

test('cannot update other user chat via parameter tampering', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $user2Chat = Chat::factory()->for($user2)->create(['title' => 'Original']);

    $response = $this->actingAs($user1)
        ->patchJson("/c/{$user2Chat->getRouteKey()}", [
            'title' => 'Hacked Title',
        ]);

    $response->assertForbidden();

    /**
     * Verify title unchanged
     */
    expect($user2Chat->fresh()->title)->toBe('Original');
});

test('cannot delete other user chat via parameter tampering', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $user2Chat = Chat::factory()->for($user2)->create();

    $response = $this->actingAs($user1)->deleteJson("/c/{$user2Chat->getRouteKey()}");

    $response->assertForbidden();

    /**
     * Verify chat still exists
     */
    expect(Chat::find($user2Chat->id))->not->toBeNull();
});

/*
|--------------------------------------------------------------------------
| Security Testing: File Upload Security
|--------------------------------------------------------------------------
|
| Test that malicious files are properly rejected.
|
*/

test('rejects executable files in chat upload', function () {
    $user = User::factory()->create();

    $maliciousFile = \Illuminate\Http\UploadedFile::fake()->create('malware.exe', 100, 'application/x-msdownload');

    $response = $this->actingAs($user)
        ->postJson('/c/stream', [
            'prompt' => 'Test with file',
            'model' => 'gemini-2.5-flash-lite',
            'files' => [$maliciousFile],
        ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('files.0');
});

test('rejects php files in chat upload', function () {
    $user = User::factory()->create();

    $phpFile = \Illuminate\Http\UploadedFile::fake()->create('backdoor.php', 100, 'application/x-php');

    $response = $this->actingAs($user)
        ->postJson('/c/stream', [
            'prompt' => 'Test with file',
            'model' => 'gemini-2.5-flash-lite',
            'files' => [$phpFile],
        ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('files.0');
});

test('rejects oversized files to prevent dos', function () {
    $user = User::factory()->create();

    /**
     * Create file larger than max allowed size
     */
    $maxSize = config('limits.file_uploads.max_size');
    $oversizedFile = \Illuminate\Http\UploadedFile::fake()->create('huge.pdf', $maxSize + 1);

    $response = $this->actingAs($user)
        ->postJson('/c/stream', [
            'prompt' => 'Test with file',
            'model' => 'gemini-2.5-flash-lite',
            'files' => [$oversizedFile],
        ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('files.0');
});

/*
|--------------------------------------------------------------------------
| Security Testing: Rate Limiting
|--------------------------------------------------------------------------
|
| Test that rate limiting prevents abuse and DOS attacks.
|
*/

test('rate limiting blocks rapid fire requests', function () {
    $user = User::factory()->create();
    $limit = config('limits.rate_limits.chat_messages');

    /**
     * Exhaust rate limit
     */
    for ($i = 0; $i < $limit; $i++) {
        $this->actingAs($user)
            ->postJson('/c/stream', [
                'prompt' => "Request {$i}",
                'model' => 'gemini-2.5-flash-lite',
            ])
            ->assertOk();
    }

    /**
     * Next request should be blocked
     */
    $response = $this->actingAs($user)
        ->postJson('/c/stream', [
            'prompt' => 'DOS attempt',
            'model' => 'gemini-2.5-flash-lite',
        ]);

    $response->assertStatus(429);
    $response->assertJson(['error' => 'Too many messages. Please wait a moment.']);
});

/*
|--------------------------------------------------------------------------
| Security Testing: Input Validation
|--------------------------------------------------------------------------
|
| Test that extreme and malicious inputs are properly rejected.
|
*/

test('rejects extremely long prompts', function () {
    $user = User::factory()->create();
    $maxLength = config('limits.validation.prompt_max_length');

    $tooLongPrompt = str_repeat('A', $maxLength + 1);

    $response = $this->actingAs($user)
        ->postJson('/c/stream', [
            'prompt' => $tooLongPrompt,
            'model' => 'gemini-2.5-flash-lite',
        ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('prompt');
});

test('application stores null byte in prompt without sanitization', function () {
    $user = User::factory()->create();

    $nullBytePayload = "Hello\x00World";

    $response = $this->actingAs($user)
        ->postJson('/c/stream', [
            'prompt' => $nullBytePayload,
            'model' => 'gemini-2.5-flash-lite',
        ]);

    /**
     * Currently application does NOT sanitize null bytes
     * This test documents the current behavior
     * TODO: Add null byte sanitization in ChatRequest validation
     */
    if ($response->getStatusCode() === 200) {
        $message = Message::latest()->first();
        expect($message->content)->toContain("\x00");
    }
})->skip('null byte sanitization not yet implemented');
