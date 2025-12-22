<?php

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use App\Models\UserUsage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Integration Testing: Complete User Flows
|--------------------------------------------------------------------------
|
| Test end-to-end scenarios that users actually perform.
|
*/

test('complete user journey: register to chat deletion', function () {
    /**
     * Step 1: User Registration
     */
    $userData = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    $registerResponse = $this->post('/register', $userData);
    $registerResponse->assertRedirect('/c');

    $user = User::where('email', 'test@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->name)->toBe('Test User');

    /**
     * Step 2: Create First Chat
     */
    $chatResponse = $this->actingAs($user)
        ->postJson('/c/stream', [
            'prompt' => 'Hello, what can you help me with?',
            'model' => 'gemini-2.5-flash-lite',
        ]);

    $chatResponse->assertOk();
    $chatId = $chatResponse->json('chat_id');
    expect($chatId)->toBeNumeric();

    $chat = Chat::find($chatId);
    expect($chat)->not->toBeNull();
    expect($chat->user_id)->toBe($user->id);

    /**
     * Step 3: Add More Messages to Same Chat
     */
    $followUpResponse = $this->actingAs($user)
        ->postJson('/c/stream', [
            'chat_id' => $chatId,
            'prompt' => 'Can you explain Laravel testing?',
            'model' => 'gemini-2.5-flash-lite',
        ]);

    $followUpResponse->assertOk();

    $messages = Message::where('chat_id', $chatId)->count();
    expect($messages)->toBeGreaterThan(0);

    /**
     * Step 4: Search for Chats
     */
    $searchResponse = $this->actingAs($user)
        ->get('/s?q=Laravel');

    $searchResponse->assertOk();
    $searchResults = $searchResponse->json();
    expect($searchResults)->toHaveKey('chats');

    /**
     * Step 5: Update Chat Title
     */
    $updateResponse = $this->actingAs($user)
        ->patchJson("/c/{$chatId}", [
            'title' => 'My Laravel Testing Discussion',
        ]);

    $updateResponse->assertRedirect();

    $chat->refresh();
    expect($chat->title)->toBe('My Laravel Testing Discussion');

    /**
     * Step 6: Verify Usage Tracking
     */
    $usageCount = UserUsage::where('user_id', $user->id)->count();
    expect($usageCount)->toBeGreaterThan(0);

    /**
     * Step 7: Delete Chat
     */
    $deleteResponse = $this->actingAs($user)
        ->delete("/c/{$chatId}");

    $deleteResponse->assertRedirect();

    $deletedChat = Chat::withTrashed()->find($chatId);
    expect($deletedChat->trashed())->toBeTrue();

    /**
     * Step 8: Verify Messages Also Soft Deleted
     */
    $deletedMessages = Message::withTrashed()
        ->where('chat_id', $chatId)
        ->get();

    foreach ($deletedMessages as $message) {
        expect($message->trashed())->toBeTrue();
    }
})->group('integration');

test('multi-chat workflow with search and pagination', function () {
    $user = User::factory()->create();

    /**
     * Step 1: Create Multiple Chats
     */
    $chatTitles = [
        'Laravel Best Practices',
        'PHP 8.4 Features',
        'Database Optimization',
        'API Design Patterns',
        'Testing Strategies',
    ];

    $createdChats = [];
    foreach ($chatTitles as $title) {
        $response = $this->actingAs($user)
            ->postJson('/c/stream', [
                'prompt' => "Tell me about {$title}",
                'model' => 'gemini-2.5-flash-lite',
            ]);

        $response->assertOk();
        $createdChats[] = $response->json('chat_id');
    }

    expect(count($createdChats))->toBe(5);

    /**
     * Step 2: Search for Specific Topic
     */
    $searchResponse = $this->actingAs($user)
        ->get('/s?q=Laravel');

    $searchResponse->assertOk();
    $results = $searchResponse->json();

    expect($results['chats'])->not->toBeEmpty();

    /**
     * Step 3: Access Chat List
     */
    $listResponse = $this->actingAs($user)->get('/');

    $listResponse->assertOk();

    /**
     * Step 4: View Specific Chat
     */
    $chatId = $createdChats[0];
    $viewResponse = $this->actingAs($user)->get("/c/{$chatId}");

    $viewResponse->assertOk();

    /**
     * Step 5: Bulk Delete
     */
    foreach (array_slice($createdChats, 0, 3) as $chatId) {
        $deleteResponse = $this->actingAs($user)->delete("/c/{$chatId}");
        $deleteResponse->assertRedirect();
    }

    /**
     * Verify 3 deleted, 2 remaining
     */
    $remainingCount = Chat::where('user_id', $user->id)->count();
    expect($remainingCount)->toBe(2);

    $deletedCount = Chat::onlyTrashed()->where('user_id', $user->id)->count();
    expect($deletedCount)->toBe(3);
})->group('integration');

test('quota limit workflow', function () {
    $user = User::factory()->create();
    $limit = config('limits.rate_limits.chat_messages');

    /**
     * Step 1: Use Up Rate Limit
     */
    for ($i = 0; $i < $limit; $i++) {
        $response = $this->actingAs($user)
            ->postJson('/c/stream', [
                'prompt' => "Message {$i}",
                'model' => 'gemini-2.5-flash-lite',
            ]);

        $response->assertOk();
    }

    /**
     * Step 2: Hit Rate Limit
     */
    $blockedResponse = $this->actingAs($user)
        ->postJson('/c/stream', [
            'prompt' => 'This should be blocked',
            'model' => 'gemini-2.5-flash-lite',
        ]);

    $blockedResponse->assertStatus(429);
    $blockedResponse->assertJson([
        'error' => 'Too many messages. Please wait a moment.',
    ]);

    /**
     * Step 3: Wait and Retry
     */
    $this->travel(61)->seconds();

    $retryResponse = $this->actingAs($user)
        ->postJson('/c/stream', [
            'prompt' => 'Should work after waiting',
            'model' => 'gemini-2.5-flash-lite',
        ]);

    $retryResponse->assertOk();
})->group('integration');

test('avatar upload and profile update flow', function () {
    $user = User::factory()->create([
        'name' => 'Original Name',
        'email' => 'original@example.com',
    ]);

    /**
     * Step 1: Update Profile Information
     */
    $profileResponse = $this->actingAs($user)
        ->patch('/profile', [
            'name' => 'Updated Name',
            'email' => 'original@example.com',
        ]);

    $profileResponse->assertRedirect();

    $user->refresh();
    expect($user->name)->toBe('Updated Name');

    /**
     * Step 2: Upload Avatar
     */
    $file = \Illuminate\Http\UploadedFile::fake()->image('avatar.jpg', 500, 500)->size(500);

    $avatarResponse = $this->actingAs($user)
        ->post('/profile/avatar', [
            'avatar' => $file,
        ]);

    $avatarResponse->assertRedirect();

    $user->refresh();
    expect($user->avatar)->not->toBeNull();

    /**
     * Step 3: Update Avatar Again (Replace)
     */
    $newFile = \Illuminate\Http\UploadedFile::fake()->image('new-avatar.jpg');

    $replaceResponse = $this->actingAs($user)
        ->post('/profile/avatar', [
            'avatar' => $newFile,
        ]);

    $replaceResponse->assertRedirect();

    /**
     * Step 4: Delete Avatar
     */
    $deleteResponse = $this->actingAs($user)
        ->delete('/profile/avatar');

    $deleteResponse->assertRedirect();

    $user->refresh();
    expect($user->avatar)->toBeNull();
})->group('integration');

test('password change and re-authentication flow', function () {
    $user = User::factory()->create([
        'password' => Hash::make('oldpassword123'),
    ]);

    /**
     * Step 1: Login with Old Password
     */
    $loginResponse = $this->post('/login', [
        'email' => $user->email,
        'password' => 'oldpassword123',
    ]);

    $loginResponse->assertRedirect('/c');

    /**
     * Step 2: Change Password
     */
    $changeResponse = $this->actingAs($user)
        ->put('/profile/password', [
            'current_password' => 'oldpassword123',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

    $changeResponse->assertRedirect();

    /**
     * Step 3: Logout
     */
    $logoutResponse = $this->actingAs($user)->post('/logout');
    $logoutResponse->assertRedirect('/c');

    /**
     * Step 4: Login with New Password
     */
    $newLoginResponse = $this->post('/login', [
        'email' => $user->email,
        'password' => 'newpassword123',
    ]);

    $newLoginResponse->assertRedirect('/c');

    /**
     * Step 5: Verify Old Password No Longer Works
     */
    $oldPasswordResponse = $this->post('/login', [
        'email' => $user->email,
        'password' => 'oldpassword123',
    ]);

    $oldPasswordResponse->assertSessionHasErrors();
})->group('integration');

test('two-factor authentication complete flow', function () {
    $user = User::factory()->create();

    /**
     * Step 1: Enable 2FA
     */
    $enableResponse = $this->actingAs($user)
        ->post('/user/two-factor-authentication');

    $enableResponse->assertRedirect();

    $user->refresh();
    expect($user->two_factor_secret)->not->toBeNull();

    /**
     * Step 2: View Recovery Codes
     */
    $codesResponse = $this->actingAs($user)
        ->get('/settings/two-factor-recovery-codes');

    $codesResponse->assertOk();

    /**
     * Step 3: Regenerate Recovery Codes
     */
    $regenerateResponse = $this->actingAs($user)
        ->post('/settings/two-factor-recovery-codes');

    $regenerateResponse->assertRedirect();

    /**
     * Step 4: Disable 2FA
     */
    $disableResponse = $this->actingAs($user)
        ->delete('/user/two-factor-authentication');

    $disableResponse->assertRedirect();

    $user->refresh();
    expect($user->two_factor_secret)->toBeNull();
})->group('integration');

test('social authentication and account linking flow', function () {
    /**
     * Step 1: Login with GitHub (Simulated)
     */
    $githubUser = (object) [
        'id' => '123456',
        'email' => 'github@example.com',
        'name' => 'GitHub User',
    ];

    /**
     * Step 2: Create user via social auth
     */
    $user = User::factory()->create([
        'email' => 'github@example.com',
        'name' => 'GitHub User',
    ]);

    $socialIdentity = \App\Models\SocialIdentity::create([
        'user_id' => $user->id,
        'provider' => 'github',
        'provider_id' => '123456',
    ]);

    expect($socialIdentity)->not->toBeNull();

    /**
     * Step 3: Verify User Can Login
     */
    $this->actingAs($user);

    $profileResponse = $this->get('/');
    $profileResponse->assertOk();

    /**
     * Step 4: Disconnect Social Account
     */
    $disconnectResponse = $this->actingAs($user)
        ->delete("/settings/social/{$socialIdentity->id}");

    $disconnectResponse->assertRedirect();

    expect(\App\Models\SocialIdentity::find($socialIdentity->id))->toBeNull();
})->group('integration');

test('complete error and recovery flow', function () {
    $user = User::factory()->create();

    /**
     * Step 1: Attempt Invalid Chat Creation
     */
    $invalidResponse = $this->actingAs($user)
        ->postJson('/c/stream', []);

    $invalidResponse->assertStatus(422);
    $invalidResponse->assertJsonValidationErrors(['prompt']);

    /**
     * Step 2: Create Valid Chat
     */
    $validResponse = $this->actingAs($user)
        ->postJson('/c/stream', [
            'prompt' => 'Valid prompt',
            'model' => 'gemini-2.5-flash-lite',
        ]);

    $validResponse->assertOk();
    $chatId = $validResponse->json('chat_id');

    /**
     * Step 3: Attempt Invalid Update
     */
    $invalidUpdateResponse = $this->actingAs($user)
        ->patchJson("/c/{$chatId}", [
            'title' => '', // Empty title
        ]);

    $invalidUpdateResponse->assertStatus(422);

    /**
     * Step 4: Valid Update
     */
    $validUpdateResponse = $this->actingAs($user)
        ->patchJson("/c/{$chatId}", [
            'title' => 'Corrected Title',
        ]);

    $validUpdateResponse->assertRedirect();

    /**
     * Step 5: Verify Final State
     */
    $chat = Chat::find($chatId);
    expect($chat->title)->toBe('Corrected Title');
})->group('integration');
