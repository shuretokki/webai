<?php

use App\Models\User;
use App\Models\Chat;
use Illuminate\Support\Facades\Config;

test('free user can use free models', function () {
    $user = User::factory()->create(['subscription_tier' => 'free']);
    $chat = Chat::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)
        ->postJson('/chat/stream', [
            'chat_id' => $chat->id,
            'prompt' => 'Hello',
            'model' => 'gemini-2.5-flash',
        ]);

    $response->assertStatus(200);
});

test('free user cannot use paid models', function () {
    $user = User::factory()->create(['subscription_tier' => 'free']);
    $chat = Chat::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)
        ->postJson('/chat/stream', [
            'chat_id' => $chat->id,
            'prompt' => 'Hello',
            'model' => 'gpt-5.2',
        ]);

    $response->assertStatus(403);
});

test('plus user can use paid models (demo mode)', function () {
    $user = User::factory()->create(['subscription_tier' => 'plus']);
    $chat = Chat::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)
        ->postJson('/chat/stream', [
            'chat_id' => $chat->id,
            'prompt' => 'Hello',
            'model' => 'gpt-5.2',
        ]);

    $response->assertStatus(200);

    $content = $response->streamedContent();
    $this->assertStringContainsString('Model', $content);
    $this->assertStringContainsString('usage', $content);
    $this->assertStringContainsString('progress', $content);
});
