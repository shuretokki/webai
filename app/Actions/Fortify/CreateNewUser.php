<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['nullable', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class)->where(function ($query) {
                    return $query->whereNotNull('email_verified_at');
                }),
            ],
            'password' => $this->passwordRules(),
        ])->validate();

        $deletedCount = User::where('email', $input['email'])
            ->whereNull('email_verified_at')
            ->delete();

        if ($deletedCount > 0) {
            \Log::info('Cleaned up unverified account on registration attempt', [
                'email' => $input['email'],
                'deleted_count' => $deletedCount,
            ]);
        }

        $name = $input['name'] ?? $this->generateNameFromEmail($input['email']);

        return User::create([
            'name' => $name,
            'email' => $input['email'],
            'password' => $input['password'],
        ]);
    }

    /**
     * Generate a user-friendly name from email address.
     */
    protected function generateNameFromEmail(string $email): string
    {
        $username = explode('@', $email)[0];
        $name = str_replace(['.', '_', '-'], ' ', $username);

        return ucwords($name);
    }
}
