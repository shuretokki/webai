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
    $response->assertJson(fn ($json) =>
        $json->has('url')
             ->etc()
    );

    $freshUser = $user->fresh();
    expect($freshUser->avatar)->not->toBeNull();
    expect($freshUser->avatar)->toBeString();
    expect($freshUser->avatar)->toContain('avatars/');
    Storage::disk('public')->assertExists($freshUser->avatar);
});

it('validates avatar is required', function () {
    $user = User::factory()->create();

    $response = actingAs($user)
        ->post(route('avatar.upload'), []);

    $response->assertSessionHasErrors('avatar');
    $response->assertStatus(302); // Redirect with validation errors

    // Verify avatar unchanged
    expect($user->fresh()->avatar)->toBeNull();
});

it('validates avatar must be an image', function () {
    $user = User::factory()->create();
    $file = UploadedFile::fake()->create('document.pdf', 100);

    $response = actingAs($user)
        ->post(route('avatar.upload'), [
            'avatar' => $file,
        ]);

    $response->assertSessionHasErrors('avatar');
    $response->assertStatus(302);

    // Verify no avatar was set
    expect($user->fresh()->avatar)->toBeNull();
});

it('validates avatar must not exceed 800kb', function () {
    $user = User::factory()->create();
    $file = UploadedFile::fake()->image('avatar.jpg')->size(900); // 900 KB

    $response = actingAs($user)
        ->post(route('avatar.upload'), [
            'avatar' => $file,
        ]);

    $response->assertSessionHasErrors('avatar');
    $response->assertStatus(302);
    expect($user->fresh()->avatar)->toBeNull();
});

it('accepts avatar at exactly 800kb', function () {
    $user = User::factory()->create();
    $file = UploadedFile::fake()->image('avatar.jpg')->size(800); // Exactly 800 KB

    $response = actingAs($user)
        ->post(route('avatar.upload'), [
            'avatar' => $file,
        ]);

    $response->assertSuccessful();
    expect($user->fresh()->avatar)->not->toBeNull();
});

it('validates avatar must be jpg, png, or gif', function () {
    $user = User::factory()->create();
    $file = UploadedFile::fake()->create('avatar.bmp', 100, 'image/bmp');

    $response = actingAs($user)
        ->post(route('avatar.upload'), [
            'avatar' => $file,
        ]);

    $response->assertSessionHasErrors('avatar');
    $response->assertStatus(302);

    // Verify in database
    $user->refresh();
    expect($user->avatar)->toBeNull();

    // Verify no file was stored
    Storage::disk('public')->assertDirectoryEmpty('avatars');
});

it('deletes old avatar when uploading new one', function () {
    $user = User::factory()->create(['avatar' => 'avatars/old-avatar.jpg']);
    $oldPath = 'avatars/old-avatar.jpg';
    Storage::disk('public')->put($oldPath, 'old content');

    $file = UploadedFile::fake()->image('avatar.jpg');

    $response = actingAs($user)
        ->post(route('avatar.upload'), [
            'avatar' => $file,
        ]);

    $response->assertSuccessful();

    // Verify old file deleted
    Storage::disk('public')->assertMissing($oldPath);

    // Verify new file exists
    $freshUser = $user->fresh();
    expect($freshUser->avatar)->not->toBe($oldPath);
    expect($freshUser->avatar)->not->toBeNull();
    Storage::disk('public')->assertExists($freshUser->avatar);
});

it('can disconnect social account', function () {
    $user = User::factory()->create();
    $socialIdentity = SocialIdentity::factory()->github()->create(['user_id' => $user->id]);

    assertDatabaseHas('social_identities', [
        'id' => $socialIdentity->id,
        'user_id' => $user->id,
        'provider' => 'github',
    ]);

    $response = actingAs($user)
        ->delete(route('social.disconnect', ['provider' => 'github']));

    $response->assertRedirect();
    $response->assertSessionHas('status');

    // Verify deleted from database
    assertDatabaseMissing('social_identities', [
        'id' => $socialIdentity->id,
    ]);

    expect(SocialIdentity::find($socialIdentity->id))->toBeNull();
    expect($user->socialIdentities()->count())->toBe(0);
});
