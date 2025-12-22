<?php

namespace App\Http\Controllers;

use App\Http\Requests\AvatarUploadRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Chat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Inertia\Response;
use ZipArchive;

class ProfileController extends Controller
{
    /**
     * Upload or update user avatar
     */
    public function uploadAvatar(AvatarUploadRequest $request): RedirectResponse
    {
        $user = $request->user();

        /**
         * Delete old avatar if exists
         */
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        /**
         * Store new avatar
         */
        $path = $request->file('avatar')->store('avatars', 'public');

        /**
         * Update user record
         */
        $user->update(['avatar' => $path]);

        return redirect()->back()->with('success', 'Avatar updated successfully.');
    }

    /**
     * Update user profile information
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        /**
         * If email changed, mark as unverified
         */
        if (isset($validated['email']) && $validated['email'] !== $user->email) {
            $validated['email_verified_at'] = null;
        }

        /**
         * Update user profile
         */
        $user->update($validated);

        /**
         * Send verification email if email changed
         */
        if (isset($validated['email']) && $validated['email'] !== $user->getOriginal('email')) {
            $user->sendEmailVerificationNotification();

            return redirect()->back()->with('success', 'Profile updated. Please verify your new email address.');
        }

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Change user password
     */
    public function changePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
        ]);

        $user = $request->user();

        /**
         * Verify current password
         */
        if (! Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }

        /**
         * Update password
         */
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->back()->with('success', 'Password changed successfully.');
    }

    /**
     * Export user data
     */
    public function exportData(Request $request): JsonResponse
    {
        $user = $request->user();

        /**
         * Gather all user data
         */
        $data = [
            'user' => $user->only(['name', 'email', 'created_at', 'updated_at']),
            'chats' => Chat::where('user_id', $user->id)
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

    /**
     * Delete user account and all data
     */
    public function destroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'string'],
        ]);

        $user = $request->user();

        /**
         * Verify password before deletion
         */
        if (! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['The password is incorrect.'],
            ]);
        }

        /**
         * Delete avatar
         */
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        /**
         * Delete all chats (will cascade delete messages and attachments)
         */
        $user->chats()->each(function ($chat) {
            $chat->forceDelete();
        });

        /**
         * Logout and delete user
         */
        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Your account has been permanently deleted.');
    }
}
