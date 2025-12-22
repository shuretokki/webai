<?php

use App\Models\User;
use App\Notifications\VerifyCurrentEmailForChange;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\patch;
use function Pest\Laravel\post;

beforeEach(function () {
    Storage::fake('public');
});

it('displays profile edit page', function () {
    $user = User::factory()->create();

    $response = actingAs($user)
        ->get(route('profile.edit'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('settings/Profile'));
});

it('updates profile name', function () {
    $user = User::factory()->create(['name' => 'Old Name']);

    $response = actingAs($user)
        ->patch(route('profile.update'), [
            'name' => 'New Name',
            'email' => $user->email,
        ]);

    $response->assertRedirect(route('profile.edit'));
    expect($user->fresh()->name)->toBe('New Name');
});

it('initiates email change with two-step verification', function () {
    Notification::fake();

    $user = User::factory()->create([
        'email' => 'old@example.com',
        'email_verified_at' => now(),
        'password' => bcrypt('password123'),
    ]);

    $response = actingAs($user)
        ->from(route('profile.edit'))
        ->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => 'new@example.com',
            'current_password' => 'password123',
        ]);

    $response->assertRedirect(route('profile.edit'));
    $response->assertSessionHas('status', 'verification-link-sent');

    // Email should NOT be changed yet
    $user->refresh();
    expect($user->email)->toBe('old@example.com');

    // But pending email should be stored
    expect($user->pending_email)->toBe('new@example.com');
    expect($user->pending_email_token)->not->toBeNull();
    expect($user->pending_email_token_expires_at)->not->toBeNull();

    // Notification should be sent to CURRENT email
    Notification::assertSentTo($user, VerifyCurrentEmailForChange::class);
});

it('requires current password to change email', function () {
    $user = User::factory()->create([
        'email' => 'old@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = actingAs($user)
        ->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => 'new@example.com',
            // Missing current_password
        ]);

    $response->assertSessionHasErrors('current_password');
    expect($user->fresh()->email)->toBe('old@example.com');
});

it('validates current password is correct when changing email', function () {
    $user = User::factory()->create([
        'email' => 'old@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = actingAs($user)
        ->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => 'new@example.com',
            'current_password' => 'wrongpassword',
        ]);

    $response->assertSessionHasErrors('current_password');
    expect($user->fresh()->email)->toBe('old@example.com');
});

it('does not require password when only changing name', function () {
    $user = User::factory()->create([
        'name' => 'Old Name',
        'email' => 'user@example.com',
    ]);

    $response = actingAs($user)
        ->patch(route('profile.update'), [
            'name' => 'New Name',
            'email' => $user->email, // Email unchanged
        ]);

    $response->assertRedirect(route('profile.edit'));
    expect($user->fresh()->name)->toBe('New Name');
});

it('keeps verification when email unchanged', function () {
    $verifiedAt = now();
    $user = User::factory()->create([
        'email' => 'same@example.com',
        'email_verified_at' => $verifiedAt,
    ]);

    actingAs($user)
        ->patch(route('profile.update'), [
            'name' => 'Updated Name',
            'email' => 'same@example.com',
        ]);

    expect($user->fresh()->email_verified_at->timestamp)->toBe($verifiedAt->timestamp);
});

it('validates profile update requires name when provided', function () {
    $user = User::factory()->create();

    $response = actingAs($user)
        ->patch(route('profile.update'), [
            'name' => '', // Empty name when provided
            'email' => $user->email,
        ]);

    $response->assertSessionHasErrors('name');
});

it('validates profile update requires valid email', function () {
    $user = User::factory()->create();

    $response = actingAs($user)
        ->patch(route('profile.update'), [
            'name' => 'Test User',
            'email' => 'invalid-email',
        ]);

    $response->assertSessionHasErrors('email');
});

it('requires authentication for profile edit', function () {
    $response = get(route('profile.edit'));

    $response->assertRedirect(route('login'));
});

it('requires authentication for profile update', function () {
    $response = patch(route('profile.update'), [
        'name' => 'Test',
        'email' => 'test@example.com',
    ]);

    $response->assertRedirect(route('login'));
});

it('requires authentication for profile deletion', function () {
    $response = delete(route('profile.destroy'));

    $response->assertRedirect(route('login'));
});

it('deletes user account with correct password', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password'),
    ]);

    $response = actingAs($user)
        ->delete(route('profile.destroy'), [
            'password' => 'password',
        ]);

    $response->assertRedirect('/');
    expect(User::find($user->id))->toBeNull();
});

it('prevents user deletion with incorrect password', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password'),
    ]);

    $response = actingAs($user)
        ->delete(route('profile.destroy'), [
            'password' => 'wrong-password',
        ]);

    $response->assertSessionHasErrors('password');
    assertDatabaseHas('users', ['id' => $user->id]);
});

it('logs out user after account deletion', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password'),
    ]);

    actingAs($user)
        ->delete(route('profile.destroy'), [
            'password' => 'password',
        ]);

    $this->assertGuest();
});

it('invalidates session after account deletion', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password'),
    ]);

    actingAs($user)
        ->delete(route('profile.destroy'), [
            'password' => 'password',
        ]);

    $response = get(route('profile.edit'));
    $response->assertRedirect(route('login'));
});

it('uploads avatar and returns url', function () {
    $user = User::factory()->create();
    $file = UploadedFile::fake()->image('avatar.jpg', 100, 100)->size(500);

    $response = actingAs($user)
        ->post(route('avatar.upload'), [
            'avatar' => $file,
        ]);

    $response->assertSuccessful();
    $response->assertJsonStructure(['url']);

    $user->refresh();
    expect($user->avatar)->not->toBeNull();
    Storage::disk('public')->assertExists($user->avatar);
});

it('requires authentication for avatar upload', function () {
    $file = UploadedFile::fake()->image('avatar.jpg');

    $response = post(route('avatar.upload'), [
        'avatar' => $file,
    ]);

    $response->assertRedirect(route('login'));
});
