<?php

use App\Models\User;
use App\Models\UserUsage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('usage is recorded when message is sent', function () {
    $initialCount = UserUsage::count();

    $response = $this->postJson('/c/stream', [
        'prompt' => 'Hello, World',
        'model' => 'gemini-2.5-flash-lite',
    ]);

    $response->assertOk();

    /* expect message_sent by user; ai_message wont be recorded
        because endpoint wont call Gemini in test; */
    $newCount = UserUsage::where('user_id', $this->user->id)->count();
    expect($newCount)->toBe($initialCount + 1);

    $messageUsage = UserUsage::where('type', 'message_sent')
        ->where('user_id', $this->user->id)
        ->latest()
        ->first();

    expect($messageUsage)->not->toBeNull();
    expect($messageUsage->user_id)->toBe($this->user->id);
    expect($messageUsage->type)->toBe('message_sent');
    expect($messageUsage->messages)->toBe(1);
    expect($messageUsage->tokens)->toBe(0);
    expect($messageUsage->bytes)->toBe(0);
    expect($messageUsage->metadata)->toBeArray();
    expect($messageUsage->metadata)->toHaveKey('model');
    expect($messageUsage->metadata['model'])->toBe('gemini-2.5-flash-lite');
    expect($messageUsage->created_at)->not->toBeNull();
});

test('quota blocks requests at limit', function () {
    UserUsage::factory()
        ->count(100)
        ->create([
            'user_id' => $this->user->id,
            'type' => 'message_sent',
            'messages' => 1,
        ]);

    $response = $this->postJson('/c/stream', [
        'prompt' => 'This should fail',
        'model' => 'gemini-2.5-flash-lite',
    ]);

    $response->assertForbidden();
    $response->assertJson([
        'error' => 'You have reached your monthly message limit. Upgrade your plan to increase limit.',
    ]);
    $response->assertJsonStructure(['error']);

    // Verify no new usage was recorded
    $count = UserUsage::where('user_id', $this->user->id)->count();
    expect($count)->toBe(100);
});

test('quota allows request just under limit', function () {
    UserUsage::factory()
        ->count(99)
        ->create([
            'user_id' => $this->user->id,
            'type' => 'message_sent',
            'messages' => 1,
        ]);

    $response = $this->postJson('/c/stream', [
        'prompt' => 'This should succeed',
        'model' => 'gemini-2.5-flash-lite',
    ]);

    $response->assertOk();

    // Verify new usage was recorded
    $count = UserUsage::where('user_id', $this->user->id)->count();
    expect($count)->toBe(100);
});

test('quota is user-specific', function () {
    $otherUser = User::factory()->create();

    // Max out first user
    UserUsage::factory()
        ->count(100)
        ->create([
            'user_id' => $this->user->id,
            'type' => 'message_sent',
            'messages' => 1,
        ]);

    // Other user should still work
    $response = $this->actingAs($otherUser)
        ->postJson('/c/stream', [
            'prompt' => 'Test',
            'model' => 'gemini-2.5-flash-lite',
        ]);

    $response->assertOk();
});

test('cost calculation is accurate for ai responses', function () {
    // Using gemini-2.5-flash: input_cost = 0.000075, output_cost = 0.0003
    // 5000 input tokens + 5000 output tokens = 10000 total
    // Cost = (5000/1000 * 0.000075) + (5000/1000 * 0.0003) = 0.000375 + 0.0015 = 0.001875
    $usage = UserUsage::record(
        userId: $this->user->id,
        type: 'ai_response',
        tokens: 10000,
        metadata: [
            'model' => 'gemini-2.5-flash',
            'input_tokens' => 5000,
            'output_tokens' => 5000,
        ]
    );

    expect($usage->cost)->toBe('0.0019'); // Rounded to 4 decimals
    expect($usage->tokens)->toBe(10000);
    expect($usage->type)->toBe('ai_response');
    expect((float) $usage->cost)->toBeGreaterThan(0.0018);
    expect((float) $usage->cost)->toBeLessThan(0.0020);
});

test('cost calculation handles zero tokens correctly', function () {
    $usage = UserUsage::record(
        userId: $this->user->id,
        type: 'ai_response',
        tokens: 0,
        metadata: [
            'model' => 'gemini-2.5-flash',
            'input_tokens' => 0,
            'output_tokens' => 0,
        ]
    );

    expect($usage->cost)->toBe('0.0000');
    expect($usage->tokens)->toBe(0);
});

test('cost calculation handles large token counts', function () {
    $usage = UserUsage::record(
        userId: $this->user->id,
        type: 'ai_response',
        tokens: 1000000,
        metadata: [
            'model' => 'gemini-2.5-flash',
            'input_tokens' => 500000,
            'output_tokens' => 500000,
        ]
    );

    expect((float) $usage->cost)->toBeGreaterThan(0);
    expect($usage->tokens)->toBe(1000000);
});

test('cost calculation is accurate for file uploads', function () {
    $oneMB = 1024 * 1024;

    $usage = UserUsage::record(
        userId: $this->user->id,
        type: 'file_upload',
        bytes: $oneMB
    );

    expect($usage->bytes)->toBe($oneMB);
    expect($usage->type)->toBe('file_upload');
    expect((float) $usage->cost)->toBeGreaterThan(0);
    expect((float) $usage->cost)->toBeLessThan(0.02);
    expect((float) $usage->cost)->toBeGreaterThan(0.01);

    // Should return at ~$0.0105
    // Verify cost is a valid decimal string
    expect($usage->cost)->toMatch('/^\d+\.\d{4}$/');
});

test('file upload cost scales with size', function () {
    $smallFile = UserUsage::record(
        userId: $this->user->id,
        type: 'file_upload',
        bytes: 100000 // ~100KB
    );

    $largeFile = UserUsage::record(
        userId: $this->user->id,
        type: 'file_upload',
        bytes: 10000000 // ~10MB
    );

    expect((float) $largeFile->cost)->toBeGreaterThan((float) $smallFile->cost);
});

test('current month usage aggregates correctly', function () {
    UserUsage::factory()
        ->create([
            'user_id' => $this->user->id,
            'type' => 'message_sent',
            'messages' => 5,
            'tokens' => 0,
        ]);

    UserUsage::factory()
        ->create([
            'user_id' => $this->user->id,
            'type' => 'ai_response',
            'messages' => 0,
            'tokens' => 1000,
        ]);

    UserUsage::factory()
        ->create([
            'user_id' => $this->user->id,
            'type' => 'message_sent',
            'messages' => 3,
            'tokens' => 0,
        ]);

    $stats = $this->user->currentMonthUsage();

    expect($stats['messages'])
        ->toBe(8);

    expect($stats['tokens'])
        ->toBe(1000);
});

test('file upload tracks bytes correctly', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()
        ->image('test.jpg', 100, 100);

    $response = $this->postJson('/c/stream', [
        'prompt' => 'Analyze this image',
        'files' => [$file],
    ]);

    $response->assertOk();

    $fileUsage = UserUsage::where(
        'type', 'file_upload'
    )->first();

    expect($fileUsage)
        ->not
        ->toBeNull();

    expect($fileUsage->bytes)
        ->toBeGreaterThan(0);

    expect($fileUsage->metadata['filename'])
        ->toBe('test.jpg');
});

test('has exceeded quota returns true when limit reached', function () {
    UserUsage::factory()
        ->count(100)
        ->create([
            'user_id' => $this->user->id,
            'type' => 'message_sent',
            'messages' => 1,
        ]);

    expect(
        $this->user
            ->hasExceededQuota('messages', 100))
        ->toBeTrue();
});

test('has exceeded quota returns false when under limit', function () {
    UserUsage::factory()
        ->count(50)
        ->create([
            'user_id' => $this->user->id,
            'type' => 'message_sent',
            'messages' => 1,
        ]);

    expect(
        $this->user
            ->hasExceededQuota('messages', 100))
        ->toBeFalse();
});
