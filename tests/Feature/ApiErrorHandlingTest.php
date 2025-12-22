<?php

use App\Models\User;

it('requires authentication for streaming', function () {
    $response = $this->post('/c/stream', [
        'prompt' => 'Hello',
        'model' => 'gemini-2.5-flash',
    ]);

    $response->assertRedirect('/login');
});

it('requires email verification for streaming', function () {
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $response = $this->actingAs($user)
        ->post('/c/stream', [
            'prompt' => 'Hello',
            'model' => 'gemini-2.5-flash',
        ]);

    $response->assertRedirect('/email/verify');
});

it('rejects invalid model selection', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($user)
        ->post('/c/stream', [
            'prompt' => 'Hello',
            'model' => 'invalid-model-xyz',
        ]);

    $response->assertStatus(400)
        ->assertJson(['error' => 'Invalid model selected.']);
});

it('enforces subscription tier requirements for premium models', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
        'subscription_tier' => 'free',
    ]);

    $premiumModels = collect(config('ai.models'))->where('is_free', false);

    if ($premiumModels->isEmpty()) {
        $this->markTestSkipped('No premium models configured for testing');
    }

    $premiumModel = $premiumModels->first();

    $response = $this->actingAs($user)
        ->post('/c/stream', [
            'prompt' => 'Hello',
            'model' => $premiumModel['id'],
        ]);

    $response->assertStatus(403)
        ->assertJson(['error' => 'This model requires a Plus or Enterprise subscription.']);
});

it('allows premium users to use premium models', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
        'subscription_tier' => 'plus',
    ]);

    $premiumModels = collect(config('ai.models'))->where('is_free', false);

    if ($premiumModels->isEmpty()) {
        $this->markTestSkipped('No premium models configured for testing');
    }

    $premiumModel = $premiumModels->first();

    $response = $this->actingAs($user)
        ->post('/c/stream', [
            'prompt' => 'Hello',
            'model' => $premiumModel['id'],
        ]);

    $response->assertStatus(200);
});

it('validates required fields for streaming', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($user)
        ->post('/c/stream', [
            'model' => 'gemini-2.5-flash',
            // Missing 'prompt'
        ]);

    $response->assertStatus(302);
});

it('returns verified users can stream chat messages', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($user)
        ->post('/c/stream', [
            'prompt' => 'Hello',
            'model' => 'gemini-2.5-flash',
        ]);

    $response->assertStatus(200);
});

it('prevents unverified users from streaming chat messages', function () {
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $response = $this->actingAs($user)
        ->post('/c/stream', [
            'prompt' => 'Hello',
            'model' => 'gemini-2.5-flash',
        ]);

    $response->assertRedirect('/email/verify');
});

test('example', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});
