<?php

use App\Models\Chat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    Storage::fake('public');
});

/**
 * Test ChatRequest validation rules with datasets
 */
test('chat request validates prompt is required', function () {
    $response = $this->postJson('/c/stream', [
        'model' => 'gemini-2.5-flash-lite',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['prompt']);
});

test('chat request validates prompt maximum length', function () {
    $longPrompt = str_repeat('a', 10001);

    $response = $this->postJson('/c/stream', [
        'prompt' => $longPrompt,
        'model' => 'gemini-2.5-flash-lite',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['prompt']);
});

test('chat request validates prompt must be string', function (mixed $invalidPrompt) {
    $response = $this->postJson('/c/stream', [
        'prompt' => $invalidPrompt,
        'model' => 'gemini-2.5-flash-lite',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['prompt']);
})->with([
    'array' => [['not', 'a', 'string']],
    'object' => [(object) ['key' => 'value']],
    'integer' => [12345],
    'boolean' => [true],
]);

test('chat request accepts valid prompt at boundary', function () {
    $validPrompt = str_repeat('a', 10000);

    $response = $this->postJson('/c/stream', [
        'prompt' => $validPrompt,
        'model' => 'gemini-2.5-flash-lite',
    ]);

    $response->assertOk();
});

test('chat request validates chat_id must exist', function () {
    $response = $this->postJson('/c/stream', [
        'prompt' => 'Test prompt',
        'chat_id' => 99999, // Non-existent
        'model' => 'gemini-2.5-flash-lite',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['chat_id']);
});

test('chat request accepts valid chat_id', function () {
    $chat = Chat::factory()->create(['user_id' => $this->user->id]);

    $response = $this->postJson('/c/stream', [
        'prompt' => 'Test prompt',
        'chat_id' => $chat->id,
        'model' => 'gemini-2.5-flash-lite',
    ]);

    $response->assertOk();
});

test('chat request validates model maximum length', function () {
    $response = $this->postJson('/c/stream', [
        'prompt' => 'Test',
        'model' => str_repeat('a', 101),
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['model']);
});

test('chat request validates file mime types', function (string $extension, string $mimeType, bool $shouldPass) {
    if (str_starts_with($mimeType, 'image/')) {
        $file = UploadedFile::fake()->image("test.{$extension}", 100, 100);
    } else {
        /**
         * Create files with actual content for non-images
         */
        $file = UploadedFile::fake()->createWithContent(
            "test.{$extension}",
            str_repeat('test content ', 100)
        );
    }

    $response = $this->postJson('/c/stream', [
        'prompt' => 'Analyze this file',
        'files' => [$file],
    ]);

    if ($shouldPass) {
        $response->assertOk();
    } else {
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['files.0']);
    }
})->with([
    'jpeg valid' => ['jpg', 'image/jpeg', true],
    'png valid' => ['png', 'image/png', true],
    'gif valid' => ['gif', 'image/gif', true],
    'pdf valid' => ['pdf', 'application/pdf', true],
    'txt valid' => ['txt', 'text/plain', true],
    'doc valid' => ['doc', 'application/msword', true],
    'docx valid' => ['docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', true],
    'exe invalid' => ['exe', 'application/x-msdownload', false],
    'zip invalid' => ['zip', 'application/zip', false],
    'svg invalid' => ['svg', 'image/svg+xml', false],
]);

test('chat request validates file size limit', function () {
    /**
     * Create image larger than limit using size() method instead of dimensions
     */
    $file = UploadedFile::fake()->image('test.jpg')->size(10241);

    $response = $this->postJson('/c/stream', [
        'prompt' => 'Analyze this file',
        'files' => [$file],
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['files.0']);
});

test('chat request accepts file at size boundary', function () {
    /**
     * Create image with content at size limit
     */
    $file = UploadedFile::fake()->image('test.jpg', 1000, 1000);

    $response = $this->postJson('/c/stream', [
        'prompt' => 'Analyze this file',
        'files' => [$file],
    ]);

    $response->assertOk();
});

test('chat request accepts multiple valid files', function () {
    $files = [
        UploadedFile::fake()->image('image1.jpg', 100, 100),
        UploadedFile::fake()->image('image2.png', 100, 100),
        UploadedFile::fake()->create('document.pdf', 500, 'application/pdf'),
    ];

    $response = $this->postJson('/c/stream', [
        'prompt' => 'Analyze these files',
        'files' => $files,
    ]);

    $response->assertOk();
});

test('chat request rejects when any file in array is invalid', function () {
    $files = [
        UploadedFile::fake()->image('valid.jpg', 100, 100),
        UploadedFile::fake()->create('invalid.exe', 100, 'application/x-msdownload'),
    ];

    $response = $this->postJson('/c/stream', [
        'prompt' => 'Analyze these files',
        'files' => $files,
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['files.1']);
});

/**
 * Test UpdateChatRequest validation rules
 */
test('update chat request validates title is required', function () {
    $chat = Chat::factory()->create(['user_id' => $this->user->id]);

    $response = $this->patchJson("/c/{$chat->id}", []);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['title']);
});

test('update chat request validates title maximum length', function () {
    $chat = Chat::factory()->create(['user_id' => $this->user->id]);

    $response = $this->patchJson("/c/{$chat->id}", [
        'title' => str_repeat('a', 256),
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['title']);
});

test('update chat request validates title must be string', function (mixed $invalidTitle) {
    $chat = Chat::factory()->create(['user_id' => $this->user->id]);

    $response = $this->patchJson("/c/{$chat->id}", [
        'title' => $invalidTitle,
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['title']);
})->with([
    'array' => [['not', 'string']],
    'object' => [(object) ['key' => 'value']],
    'integer' => [123],
    'boolean' => [false],
]);

test('update chat request accepts title at boundary', function () {
    $chat = Chat::factory()->create(['user_id' => $this->user->id]);

    $response = $this->patchJson("/c/{$chat->id}", [
        'title' => str_repeat('a', 255),
    ]);

    $response->assertOk();

    $chat->refresh();
    expect($chat->title)->toHaveLength(255);
});

test('update chat request enforces authorization', function () {
    $otherUser = User::factory()->create();
    $chat = Chat::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->patchJson("/c/{$chat->id}", [
        'title' => 'Unauthorized Update',
    ]);

    $response->assertForbidden();

    // Verify title unchanged
    $chat->refresh();
    expect($chat->title)->not->toBe('Unauthorized Update');
});
