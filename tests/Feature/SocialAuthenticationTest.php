<?php

use App\Models\SocialIdentity;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;

beforeEach(function () {
    $this->mockSocialiteUser = mock(SocialiteUser::class);
});

it('redirects to github oauth provider', function () {
    Socialite::shouldReceive('driver')
        ->with('github')
        ->once()
        ->andReturnSelf();

    Socialite::shouldReceive('redirect')
        ->once()
        ->andReturn(redirect('https://github.com/oauth'));

    $response = get('/auth/github/redirect');

    $response->assertRedirect();
});

it('redirects to google oauth provider', function () {
    Socialite::shouldReceive('driver')
        ->with('google')
        ->once()
        ->andReturnSelf();

    Socialite::shouldReceive('redirect')
        ->once()
        ->andReturn(redirect('https://accounts.google.com/oauth'));

    $response = get('/auth/google/redirect');

    $response->assertRedirect();
});

it('logs in existing user with social account', function () {
    $user = User::factory()->create();
    $socialIdentity = SocialIdentity::factory()->github()->create([
        'user_id' => $user->id,
        'provider_id' => '12345',
    ]);

    $this->mockSocialiteUser->shouldReceive('getId')->andReturn('12345');
    $this->mockSocialiteUser->shouldReceive('getEmail')->andReturn($user->email);
    $this->mockSocialiteUser->shouldReceive('getAvatar')->andReturn('https://avatar.url');
    $this->mockSocialiteUser->token = 'new-token';

    Socialite::shouldReceive('driver')
        ->with('github')
        ->once()
        ->andReturnSelf();

    Socialite::shouldReceive('user')
        ->once()
        ->andReturn($this->mockSocialiteUser);

    $response = get('/auth/github/callback');

    $response->assertRedirect('/chat');
    $this->assertAuthenticatedAs($user);

    assertDatabaseHas('social_identities', [
        'id' => $socialIdentity->id,
        'provider_token' => 'new-token',
        'avatar_url' => 'https://avatar.url',
    ]);
});

it('links social account to existing user by email', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    $this->mockSocialiteUser->shouldReceive('getId')->andReturn('12345');
    $this->mockSocialiteUser->shouldReceive('getEmail')->andReturn('test@example.com');
    $this->mockSocialiteUser->shouldReceive('getAvatar')->andReturn('https://avatar.url');
    $this->mockSocialiteUser->token = 'github-token';

    Socialite::shouldReceive('driver')
        ->with('github')
        ->once()
        ->andReturnSelf();

    Socialite::shouldReceive('user')
        ->once()
        ->andReturn($this->mockSocialiteUser);

    $response = get('/auth/github/callback');

    $response->assertRedirect('/chat');
    $this->assertAuthenticatedAs($user);

    assertDatabaseHas('social_identities', [
        'user_id' => $user->id,
        'provider_name' => 'github',
        'provider_id' => '12345',
        'provider_token' => 'github-token',
    ]);
});

it('creates new user from social account', function () {
    $this->mockSocialiteUser->shouldReceive('getId')->andReturn('67890');
    $this->mockSocialiteUser->shouldReceive('getEmail')->andReturn('newuser@example.com');
    $this->mockSocialiteUser->shouldReceive('getName')->andReturn('New User');
    $this->mockSocialiteUser->shouldReceive('getNickname')->andReturn('newuser');
    $this->mockSocialiteUser->shouldReceive('getAvatar')->andReturn('https://avatar.url');
    $this->mockSocialiteUser->token = 'google-token';

    Socialite::shouldReceive('driver')
        ->with('google')
        ->once()
        ->andReturnSelf();

    Socialite::shouldReceive('user')
        ->once()
        ->andReturn($this->mockSocialiteUser);

    $response = get('/auth/google/callback');

    $response->assertRedirect('/chat');

    assertDatabaseHas('users', [
        'email' => 'newuser@example.com',
        'name' => 'New User',
        'avatar' => 'https://avatar.url',
    ]);

    assertDatabaseHas('social_identities', [
        'provider_name' => 'google',
        'provider_id' => '67890',
        'provider_token' => 'google-token',
    ]);

    $user = User::where('email', 'newuser@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->name)->toBe('New User');
    expect($user->avatar)->toBe('https://avatar.url');
});

it('sets avatar from social provider if user has no avatar', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'avatar' => null,
    ]);

    $this->mockSocialiteUser->shouldReceive('getId')->andReturn('12345');
    $this->mockSocialiteUser->shouldReceive('getEmail')->andReturn('test@example.com');
    $this->mockSocialiteUser->shouldReceive('getAvatar')->andReturn('https://new-avatar.url');
    $this->mockSocialiteUser->token = 'token';

    Socialite::shouldReceive('driver')
        ->with('github')
        ->once()
        ->andReturnSelf();

    Socialite::shouldReceive('user')
        ->once()
        ->andReturn($this->mockSocialiteUser);

    get('/auth/github/callback');

    expect($user->fresh()->avatar)->toBe('https://new-avatar.url');
});

it('does not overwrite existing avatar', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'avatar' => 'existing-avatar.jpg',
    ]);
    $socialIdentity = SocialIdentity::factory()->github()->create([
        'user_id' => $user->id,
        'provider_id' => '12345',
    ]);

    $this->mockSocialiteUser->shouldReceive('getId')->andReturn('12345');
    $this->mockSocialiteUser->shouldReceive('getEmail')->andReturn('test@example.com');
    $this->mockSocialiteUser->shouldReceive('getAvatar')->andReturn('https://new-avatar.url');
    $this->mockSocialiteUser->token = 'token';

    Socialite::shouldReceive('driver')
        ->with('github')
        ->once()
        ->andReturnSelf();

    Socialite::shouldReceive('user')
        ->once()
        ->andReturn($this->mockSocialiteUser);

    get('/auth/github/callback');

    expect($user->fresh()->avatar)->toBe('existing-avatar.jpg');
});

it('disconnects social account successfully', function () {
    $user = User::factory()->create();
    $socialIdentity = SocialIdentity::factory()->github()->create(['user_id' => $user->id]);

    $response = actingAs($user)
        ->delete("/auth/github/disconnect");

    $response->assertRedirect();
    $response->assertSessionHas('status', 'Github account disconnected.');

    expect(SocialIdentity::find($socialIdentity->id))->toBeNull();
});

it('handles disconnect when no social account exists', function () {
    $user = User::factory()->create();

    $response = actingAs($user)
        ->delete("/auth/google/disconnect");

    $response->assertRedirect();
    $response->assertSessionHas('status', 'Google account disconnected.');
});

it('requires authentication to disconnect account', function () {
    $response = delete('/auth/github/disconnect');

    $response->assertRedirect(route('login'));
});

it('uses nickname as fallback when name is not provided', function () {
    $this->mockSocialiteUser->shouldReceive('getId')->andReturn('99999');
    $this->mockSocialiteUser->shouldReceive('getEmail')->andReturn('nickname@example.com');
    $this->mockSocialiteUser->shouldReceive('getName')->andReturn(null);
    $this->mockSocialiteUser->shouldReceive('getNickname')->andReturn('coolnickname');
    $this->mockSocialiteUser->shouldReceive('getAvatar')->andReturn(null);
    $this->mockSocialiteUser->token = 'token';

    Socialite::shouldReceive('driver')
        ->with('github')
        ->once()
        ->andReturnSelf();

    Socialite::shouldReceive('user')
        ->once()
        ->andReturn($this->mockSocialiteUser);

    get('/auth/github/callback');

    assertDatabaseHas('users', [
        'email' => 'nickname@example.com',
        'name' => 'coolnickname',
    ]);
});
