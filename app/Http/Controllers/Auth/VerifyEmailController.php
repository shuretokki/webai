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
        $user = $request->user();

        if (! $user) {
            $user = \App\Models\User::findOrFail($request->route('id'));
        }

        if (! hash_equals(
            (string) $request->route('hash'),
            sha1($user->getEmailForVerification())
        )) {
            return redirect('/login')->with('error', 'Invalid verification link.');
        }

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        if (! Auth::check()) {
            Auth::login($user);
            $request->session()->regenerate();
        }

        return redirect()->intended(config('fortify.home', '/c'))
            ->with('verified', true)
            ->with('status', 'Your email has been verified!');
    }
}
