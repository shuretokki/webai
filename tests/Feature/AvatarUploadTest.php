<?php

use App\Models\SocialIdentity;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\delete;
use function Pest\Laravel\post;

beforeEach(function () {
    Storage::fake('public');
});

it('can upload avatar successfully', function () {
    $user = User::factory()->create();
    $file = UploadedFile::fake()->image('avatar.jpg', 100, 100)->size(500);

    $response = actingAs($user)
        ->post(route('avatar.upload'), [
            'avatar' => $file,
        ]);

    $response->assertSuccessful();
    $response->assertJsonStructure(['url']);

    expect($user->fresh()->avatar)->not->toBeNull();
    Storage::disk('public')->assertExists($user->fresh()->avatar);
});

it('validates avatar is required', function () {
    $user = User::factory()->create();

    $response = actingAs($user)
        ->post(route('avatar.upload'), []);

    $response->assertSessionHasErrors('avatar');
});

it('validates avatar must be an image', function () {
    $user = User::factory()->create();
    $file = UploadedFile::fake()->create('document.pdf', 100);

    $response = actingAs($user)
        ->post(route('avatar.upload'), [
            'avatar' => $file,
        ]);

    $response->assertSessionHasErrors('avatar');
});

it('validates avatar must not exceed 800kb', function () {
    $user = User::factory()->create();
    $file = UploadedFile::fake()->image('avatar.jpg')->size(900);

    $response = actingAs($user)
        ->post(route('avatar.upload'), [
            'avatar' => $file,
        ]);

    $response->assertSessionHasErrors('avatar');
});

it('validates avatar must be jpg, png, or gif', function () {
    $user = User::factory()->create();
    $file = UploadedFile::fake()->create('avatar.bmp', 100, 'image/bmp');

    $response = actingAs($user)
        ->post(route('avatar.upload'), [
            'avatar' => $file,
        ]);

    $response->assertSessionHasErrors('avatar');
});

it('deletes old avatar when uploading new one', function () {
    $user = User::factory()->create(['avatar' => 'avatars/old-avatar.jpg']);
    Storage::disk('public')->put('avatars/old-avatar.jpg', 'old content');

    $file = UploadedFile::fake()->image('avatar.jpg');

    actingAs($user)
        ->post(route('avatar.upload'), [
            'avatar' => $file,
        ]);

    Storage::disk('public')->assertMissing('avatars/old-avatar.jpg');
    Storage::disk('public')->assertExists($user->fresh()->avatar);
});

it('can disconnect social account', function () {
    $user = User::factory()->create();
    $socialIdentity = SocialIdentity::factory()->github()->create(['user_id' => $user->id]);

    assertDatabaseHas('social_identities', [
        'id' => $socialIdentity->id,
        'user_id' => $user->id,
    ]);

    actingAs($user)
        ->delete(route('social.disconnect', ['provider' => 'github']))
        ->assertRedirect();

    expect(SocialIdentity::find($socialIdentity->id))->toBeNull();
});
