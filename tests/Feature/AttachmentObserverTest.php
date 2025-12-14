<?php

use App\Models\Attachment;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()
        ->create();
    $this->actingAs($this->user);

    Storage::fake('public');
});

test('file is deleted from disk when attachment is force deleted', function () {
    $chat = Chat::factory()
        ->create([
            'user_id' => $this->user->id,
        ]);

    $message = Message::factory()
        ->create([
            'chat_id' => $chat->id,
        ]);

    $file = UploadedFile::fake()
        ->image('test.jpg');

    $path = $file
        ->store('attachments', 'public');

    $attachment = Attachment::create([
        'message_id' => $message->id,
        'name' => 'test.jpg',
        'path' => $path,
        'mime_type' => 'image/jpeg',
        'size' => $file->getSize(),
    ]);

    expect(
        Storage::disk('public')
            ->exists($path))
        ->toBeTrue();

    $attachment->forceDelete();

    expect(
        Storage::disk('public')
            ->exists($path))
        ->toBeFalse();
});

test('file persists when attachment is soft deleted', function () {
    $chat = Chat::factory()
        ->create([
            'user_id' => $this->user->id,
        ]);

    $message = Message::factory()
        ->create([
            'chat_id' => $chat->id,
        ]);

    $file = UploadedFile::fake()
        ->image('test.jpg');

    $path = $file
        ->store('attachments', 'public');

    $attachment = Attachment::create([
        'message_id' => $message->id,
        'name' => 'test.jpg',
        'path' => $path,
        'mime_type' => 'image/jpeg',
        'size' => $file->getSize(),
    ]);

    expect(
        Storage::disk('public')
            ->exists($path))
        ->toBeTrue();

    $attachment->delete();

    expect(
        Storage::disk('public')
            ->exists($path))
        ->toBeTrue();

    expect($attachment->trashed())
        ->toBeTrue();
});

test('files are deleted when chat is deleted via cascade', function () {
    $chat = Chat::factory()
        ->create([
            'user_id' => $this->user->id,
        ]);

    $message = Message::factory()
        ->create([
            'chat_id' => $chat->id,
        ]);

    $paths = [];
    for ($i = 0; $i < 3; $i++) {
        $file = UploadedFile::fake()
            ->image("file{$i}.jpg");

        $path = $file
            ->store('attachments', 'public');

        $paths[] = $path;

        Attachment::create([
            'message_id' => $message->id,
            'name' => "file{$i}.jpg",
            'path' => $path,
            'mime_type' => 'image/jpeg',
            'size' => $file->getSize(),
        ]);
    }

    foreach ($paths as $path) {
        expect(
            Storage::disk('public')
                ->exists($path))
            ->toBeTrue();
    }

    $chat->delete();

    foreach ($paths as $path) {
        expect(
            Storage::disk('public')
                ->exists($path))
            ->toBeTrue();
    }

    $chat->forceDelete();

    foreach ($paths as $path) {
        expect(
            Storage::disk('public')
                ->exists($path))
            ->toBeFalse();
    }
});
