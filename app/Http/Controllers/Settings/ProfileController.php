<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\AvatarUploadRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Notifications\VerifyCurrentEmailForChange;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{

    public function edit(Request $request): Response
    {
        return Inertia::render('settings/Profile', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => $request->session()->get('status'),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        if ($request->filled('email') && $request->email !== $user->email) {
            $request->validate([
                'current_password' => ['required', 'current_password'],
            ], [
                'current_password.required' => 'Please enter your current password to change your email.',
                'current_password.current_password' => 'The provided password does not match your current password.',
            ]);

            if (\App\Models\User::where('email', $request->email)->where('id', '!=', $user->id)->exists()) {
                return back()->withErrors(['email' => 'This email is already in use.']);
            }

            $token = Str::random(64);

            $user->update([
                'pending_email' => $request->email,
                'pending_email_token' => hash('sha256', $token),
                'pending_email_token_expires_at' => now()->addHour(),
            ]);

            $user->notify(new VerifyCurrentEmailForChange($request->email, $token));

            return back()->with('status', 'verification-link-sent');
        }

        if ($request->filled('name')) {
            $user->update(['name' => $request->name]);
        }

        return to_route('profile.edit');
    }

    public function verifyCurrentEmail(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Validate the token
        $token = $request->query('token');

        if (! $token ||
            ! $user->pending_email ||
            ! $user->pending_email_token ||
            $user->pending_email_token_expires_at < now() ||
            ! hash_equals($user->pending_email_token, hash('sha256', $token))) {

            return redirect()->route('profile.edit')->withErrors([
                'email' => 'This email verification link is invalid or has expired.'
            ]);
        }

        // Now send verification email to the NEW email address
        $user->email = $user->pending_email;
        $user->email_verified_at = null; // Will be set when they verify the new email
        $user->pending_email = null;
        $user->pending_email_token = null;
        $user->pending_email_token_expires_at = null;
        $user->save();

        // Trigger Laravel's built-in email verification
        $user->sendEmailVerificationNotification();

        return redirect()->route('profile.edit')->with('status', 'email-changed-verify-new');
    }

    public function cancelEmailChange(Request $request): RedirectResponse
    {
        $user = $request->user();

        $user->update([
            'pending_email' => null,
            'pending_email_token' => null,
            'pending_email_token_expires_at' => null,
        ]);

        return back()->with('status', 'email-change-cancelled');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function uploadAvatar(AvatarUploadRequest $request)
    {
        $user = $request->user();
        $oldAvatar = $user->getRawOriginal('avatar');

        if ($oldAvatar && Storage::disk('public')->exists($oldAvatar)) {
            Storage::disk('public')->delete($oldAvatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');

        $user->update(['avatar' => $path]);

        if ($request->wantsJson()) {
            return response()->json([
                'url' => Storage::disk('public')->url($path),
            ]);
        }

        return back();
    }

    public function changePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', \Illuminate\Validation\Rules\Password::defaults(), 'confirmed'],
        ]);

        $user = $request->user();

        if (! \Illuminate\Support\Facades\Hash::check($validated['current_password'], $user->password)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }

        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
        ]);

        return redirect()->back()->with('success', 'Password changed successfully.');
    }

    public function exportData(Request $request): JsonResponse
    {
        $user = $request->user();

        $data = [
            'user' => $user->only(['name', 'email', 'created_at', 'updated_at']),
            'chats' => \App\Models\Chat::where('user_id', $user->id)
                ->with('messages.attachments')
                ->get()
                ->toArray(),
            'usage' => $user->usage()->get()->toArray(),
        ];

        return response()->json($data, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="user-data-'.$user->id.'.json"',
        ]);
    }
}
