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
    $response = $this->postJson('/chat/stream', [
        'prompt' => 'Hello, World',
        'model' => 'gemini-2.0-flash-lite',
    ]);

    $response->assertOk();

    /* expect message_sent by user; ai_message wont be recorded
        because endpoint wont call Gemini in test; */
    expect(UserUsage::where(
        'user_id', $this->user->id)->count())
        ->toBe(1);

    $messageUsage =
        UserUsage::where(
            'type', 'message_sent')
            ->first();

    expect($messageUsage)
        ->not
        ->toBeNull();

    expect($messageUsage->messages)
        ->toBe(1);

    expect($messageUsage->metadata['model'])
        ->toBe('gemini-2.0-flash-lite');
});

test('quota blocks requests at limit', function () {
    UserUsage::factory()
        ->count(100)
        ->create([
            'user_id' => $this->user->id,
            'type' => 'message_sent',
            'messages' => 1,
        ]);

    $response = $this->postJson('/chat/stream', [
        'prompt' => 'This should fail',
        'model' => 'gemini-2.0-flash-lite',
    ]);

    $response->assertStatus(403);
    $response->assertJson(
        ['error' => 'You have reached your monthly message limit. Upgrade your plan to increase limit.']);
});

test('cost calculation is accurate for ai responses', function () {
    $usage = UserUsage::record(
        userId: $this->user->id,
        type: 'ai_response',
        tokens: 10000
    );

    expect($usage->cost)
        ->toBe('1.0000');
});

test('cost calculation is accurate for file uploads', function () {
    $oneMB = 1024 * 1024;

    $usage = UserUsage::record(
        userId: $this->user->id,
        type: 'file_upload',
        bytes: $oneMB
    );

    expect((float) $usage->cost)
        ->toBeGreaterThan(0);

    expect((float) $usage->cost)
        ->toBeLessThan(0.02);

    // Should return at ~$0.0105
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

    $response = $this->postJson('/chat/stream', [
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
