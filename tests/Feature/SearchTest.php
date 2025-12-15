<?php

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->otherUser = User::factory()->create();
});

test('returns empty results for non-matching query', function () {
    Chat::factory()
        ->for($this->user)
        ->create(['title' => 'Laravel Development']);

    $response = $this->actingAs($this->user)
        ->getJson('/chat/search?q=nonexistent');

    $response->assertOk()
        ->assertJson([
            'results' => [],
        ]);
});

test('finds chats by title', function () {
    $chat1 = Chat::factory()
        ->for($this->user)
        ->create(['title' => 'Laravel Testing Guide']);

    $chat2 = Chat::factory()
        ->for($this->user)
        ->create(['title' => 'Vue Component Tutorial']);

    $chat3 = Chat::factory()
        ->for($this->user)
        ->create(['title' => 'Laravel Policies']);

    $response = $this->actingAs($this->user)
        ->getJson('/chat/search?q=Laravel');

    $response->assertOk();

    $results = $response->json('results');
    $chats = collect($results)->where('type', 'chat')->values();

    expect($chats->count())->toBe(2);

    $chatIds = $chats->pluck('id')->toArray();
    expect($chatIds)->toContain($chat1->id, $chat3->id);
    expect($chatIds)->not->toContain($chat2->id);
});

test('finds messages by content', function () {
    $chat = Chat::factory()
        ->for($this->user)
        ->create(['title' => 'My Chat']);

    $message1 = Message::factory()
        ->for($chat)
        ->create([
            'role' => 'user',
            'content' => 'How do I implement policies in Laravel?',
        ]);

    $message2 = Message::factory()
        ->for($chat)
        ->create([
            'role' => 'assistant',
            'content' => 'Policies in Laravel are classes that organize authorization logic.',
        ]);

    $message3 = Message::factory()
        ->for($chat)
        ->create([
            'role' => 'user',
            'content' => 'Tell me about Vue components',
        ]);

    $response = $this->actingAs($this->user)
        ->getJson('/chat/search?q=policies');

    $response->assertOk();

    $results = $response->json('results');
    $messages = collect($results)->where('type', 'message')->values();

    expect($messages->count())->toBe(2);

    $messageIds = $messages->pluck('id')->toArray();
    expect($messageIds)->toContain($message1->id, $message2->id);
    expect($messageIds)->not->toContain($message3->id);
});

test('limits results correctly', function () {
    // Create 7 chats (should return max 5)
    for ($i = 1; $i <= 7; $i++) {
        Chat::factory()
            ->for($this->user)
            ->create(['title' => "Laravel Tutorial $i"]);
    }

    $chat = Chat::factory()
        ->for($this->user)
        ->create(['title' => 'Laravel Messages']);

    // Create 12 messages (should return max 10)
    for ($i = 1; $i <= 12; $i++) {
        Message::factory()
            ->for($chat)
            ->create([
                'role' => 'user',
                'content' => "Laravel question number $i",
            ]);
    }

    $response = $this->actingAs($this->user)
        ->getJson('/chat/search?q=Laravel');

    $response->assertOk();

    $results = collect($response->json('results'));
    $chats = $results->where('type', 'chat');
    $messages = $results->where('type', 'message');

    // Max 5 chats
    expect($chats->count())->toBeLessThanOrEqual(5);
    // Max 10 messages
    expect($messages->count())->toBeLessThanOrEqual(10);
    // Total results max 15
    expect($results->count())->toBeLessThanOrEqual(15);
});

test('prevents SQL injection via special character escaping', function () {
    // Create test data
    $chat1 = Chat::factory()
        ->for($this->user)
        ->create(['title' => 'Laravel Guide']);

    $chat2 = Chat::factory()
        ->for($this->user)
        ->create(['title' => 'PHP Tutorial']);

    Message::factory()
        ->for($chat1)
        ->create([
            'role' => 'user',
            'content' => 'How do I use Laravel?',
        ]);

    Message::factory()
        ->for($chat2)
        ->create([
            'role' => 'user',
            'content' => 'PHP is great!',
        ]);

    // Test that % doesn't act as a wildcard (which would match everything)
    // If escaping is broken, "%" would match all 2 chats
    $response = $this->actingAs($this->user)
        ->getJson('/chat/search?q=%');

    $response->assertOk();
    $results = collect($response->json('results'));

    // Should return 0 results because no titles contain literal "%"
    // If % was treated as wildcard, it would match everything
    expect($results->count())->toBe(0);

    // Test that _ doesn't act as a single-character wildcard
    // If escaping is broken, "P_P" would match "PHP"
    $response = $this->actingAs($this->user)
        ->getJson('/chat/search?q=P_P');

    $response->assertOk();
    $results = collect($response->json('results'));

    // Should return 0 because no content has literal "P_P"
    // If _ was a wildcard, it would match "PHP"
    expect($results->count())->toBe(0);
});

test('only returns current users data', function () {
    // Current user's data
    $userChat = Chat::factory()
        ->for($this->user)
        ->create(['title' => 'My Laravel Project']);

    Message::factory()
        ->for($userChat)
        ->create([
            'role' => 'user',
            'content' => 'My Laravel question',
        ]);

    // Other user's data
    $otherChat = Chat::factory()
        ->for($this->otherUser)
        ->create(['title' => 'Other Laravel Project']);

    Message::factory()
        ->for($otherChat)
        ->create([
            'role' => 'user',
            'content' => 'Other Laravel question',
        ]);

    $response = $this->actingAs($this->user)
        ->getJson('/chat/search?q=Laravel');

    $response->assertOk();

    $results = collect($response->json('results'));
    $chats = $results->where('type', 'chat');
    $messages = $results->where('type', 'message');

    // Should only return current user's data
    expect($chats->count())->toBe(1);
    expect($messages->count())->toBe(1);
    expect($chats->first()['id'])->toBe($userChat->id);
    expect($messages->first()['id'])->toBeGreaterThan(0); // Just verify it exists
});

test('requires authentication', function () {
    $response = $this->getJson('/chat/search?q=test');

    $response->assertUnauthorized();
});
