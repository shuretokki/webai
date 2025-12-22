<?php

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

it('redirects unverified users to verification page when accessing protected routes', function () {
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $this->actingAs($user)
        ->get('/c')
        ->assertRedirect('/email/verify');
});

it('allows verified users to access protected routes', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $this->actingAs($user)
        ->get('/c')
        ->assertStatus(200);
});

it('sends verification email on registration', function () {
    Notification::fake();

    $this->post('/register', [
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $user = User::where('email', 'test@example.com')->first();

    expect($user)->not->toBeNull();
    expect($user->email_verified_at)->toBeNull();
    expect($user->name)->toBe('Test');

    Notification::assertSentTo($user, VerifyEmail::class);
});

it('can resend verification email', function () {
    Notification::fake();

    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $this->actingAs($user)
        ->post('/email/verification-notification')
        ->assertRedirect()
        ->assertSessionHas('status', 'verification-link-sent');

    Notification::assertSentTo($user, VerifyEmail::class);
});

it('verifies email with valid link', function () {
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );

    $this->actingAs($user)
        ->get($verificationUrl)
        ->assertRedirect('/c?verified=1');

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});

it('does not verify email with invalid hash', function () {
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => 'invalid-hash']
    );

    $this->actingAs($user)
        ->get($verificationUrl)
        ->assertStatus(403);

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

it('auto-generates name from email when not provided', function () {
    Notification::fake();

    $email = 'john.doe@example.com';

    $this->post('/register', [
        'email' => $email,
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $user = User::where('email', $email)->first();

    expect($user)->not->toBeNull();
    expect($user->name)->toBe('John Doe');
});

it('prevents duplicate registration for unverified email within 24 hours', function () {
    $oldUser = User::factory()->create([
        'email' => 'test@example.com',
        'email_verified_at' => null,
        'created_at' => now(),
    ]);

    // Try to register again with same email - should succeed and delete old account
    $response = $this->post('/register', [
        'email' => 'test@example.com',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    $response->assertRedirect();

    // Old user should be deleted
    expect(User::find($oldUser->id))->toBeNull();

    // New user should exist
    $newUser = User::where('email', 'test@example.com')->first();
    expect($newUser)->not->toBeNull();
    expect($newUser->id)->not->toBe($oldUser->id);
});

it('allows re-registration after 24 hours if email not verified', function () {
    // Create old unverified user
    $oldUser = User::factory()->create([
        'email' => 'test@example.com',
        'email_verified_at' => null,
        'created_at' => now()->subHours(25),
    ]);

    // Should allow new registration
    $response = $this->post('/register', [
        'email' => 'test@example.com',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    $response->assertRedirect();

    // Old user should be deleted
    expect(User::find($oldUser->id))->toBeNull();

    // New user should exist
    $newUser = User::where('email', 'test@example.com')->first();
    expect($newUser)->not->toBeNull();
    expect($newUser->id)->not->toBe($oldUser->id);
});

it('does not prevent registration for verified emails', function () {
    User::factory()->create([
        'email' => 'verified@example.com',
        'email_verified_at' => now(),
    ]);

    $response = $this->post('/register', [
        'email' => 'verified@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors(['email']);
});

it('cleanup command deletes unverified users older than 24 hours', function () {
    // Create mix of users
    $oldUnverified = User::factory()->create([
        'email' => 'old@example.com',
        'email_verified_at' => null,
        'created_at' => now()->subHours(25),
    ]);

    $recentUnverified = User::factory()->create([
        'email' => 'recent@example.com',
        'email_verified_at' => null,
        'created_at' => now()->subHours(12),
    ]);

    $verified = User::factory()->create([
        'email' => 'verified@example.com',
        'email_verified_at' => now(),
        'created_at' => now()->subHours(30),
    ]);

    $this->artisan('users:cleanup-unverified')
        ->assertSuccessful();

    expect(User::find($oldUnverified->id))->toBeNull();
    expect(User::find($recentUnverified->id))->not->toBeNull();
    expect(User::find($verified->id))->not->toBeNull();
});

test('example', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});
