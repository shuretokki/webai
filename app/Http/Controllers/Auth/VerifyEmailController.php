<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $urlUserId = $request->route('id');
        $user = $request->user();

        if ($user && $user->id != $urlUserId) {
            abort(403, 'Invalid verification link.');
        }

        if (! $user) {
            $user = \App\Models\User::findOrFail($urlUserId);
        }

        if (! hash_equals(
            (string) $request->route('hash'),
            sha1($user->getEmailForVerification())
        )) {
            abort(403, 'Invalid verification link.');
        }

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        if (! Auth::check()) {
            Auth::login($user);
            $request->session()->regenerate();
        }

        $request->session()->forget('pending_verification_user_id');

        $redirectUrl = config('fortify.home', '/c');

        return redirect($redirectUrl.'?verified=1')
            ->with('verified', true)
            ->with('status', 'Your email has been verified!');
    }
}
