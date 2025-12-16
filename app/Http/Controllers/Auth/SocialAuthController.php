<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SocialIdentity;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirect(string $provider): RedirectResponse
    {
        $this->validateProvider($provider);

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        $this->validateProvider($provider);

        $socialUser = Socialite::driver($provider)->user();

        $socialIdentity = SocialIdentity::where('provider_name', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if ($socialIdentity) {
            $user = $socialIdentity->user;

            $socialIdentity->update([
                'provider_token' => $socialUser->token,
                'avatar_url' => $socialUser->getAvatar(),
            ]);

            if (! $user->avatar && $socialUser->getAvatar()) {
                $user->update(['avatar' => $socialUser->getAvatar()]);
            }

            Auth::login($user);

            return redirect()->intended('/chat');
        }

        $user = User::where('email', $socialUser->getEmail())->first();

        if ($user) {
            $this->linkSocialAccount($user, $provider, $socialUser);

            Auth::login($user);

            return redirect()->intended('/chat');
        }

        $user = $this->createUserFromSocial($provider, $socialUser);

        Auth::login($user);

        return redirect()->intended('/chat');
    }

    public function disconnect(string $provider): RedirectResponse
    {
        $this->validateProvider($provider);

        $user = Auth::user();

        $socialIdentity = $user->socialIdentities()
            ->where('provider_name', $provider)
            ->first();

        if ($socialIdentity) {
            $socialIdentity->delete();
        }

        return back()->with('status', ucfirst($provider).' account disconnected.');
    }

    protected function validateProvider(string $provider): void
    {
        if (! in_array($provider, ['github', 'google'])) {
            abort(404);
        }
    }

    protected function linkSocialAccount(User $user, string $provider, $socialUser): void
    {
        $user->socialIdentities()->create([
            'provider_name' => $provider,
            'provider_id' => $socialUser->getId(),
            'provider_token' => $socialUser->token,
            'avatar_url' => $socialUser->getAvatar(),
        ]);

        if (! $user->avatar && $socialUser->getAvatar()) {
            $user->update(['avatar' => $socialUser->getAvatar()]);
        }
    }

    protected function createUserFromSocial(string $provider, $socialUser): User
    {
        $user = User::create([
            'name' => $socialUser->getName() ?? $socialUser->getNickname(),
            'email' => $socialUser->getEmail(),
            'password' => Hash::make(Str::random(32)),
            'email_verified_at' => now(),
            'avatar' => $socialUser->getAvatar(),
        ]);

        $user->socialIdentities()->create([
            'provider_name' => $provider,
            'provider_id' => $socialUser->getId(),
            'provider_token' => $socialUser->token,
            'avatar_url' => $socialUser->getAvatar(),
        ]);

        return $user;
    }
}
