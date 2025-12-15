# Backend Implementation Notes

## Profile Photo Upload
The frontend currently implements a mock function for profile photo uploads in `resources/js/pages/settings/Profile.vue`.

**Required API Endpoint:**
- **Method:** POST
- **URL:** `/api/user/avatar` (or similar)
- **Body:** `FormData` containing the file (key: `avatar` or `photo`).

**Expected Behavior:**
1.  Validate image type (jpg, png, gif) and size (max 800KB recommended).
2.  Store image (S3 or local storage).
3.  Update `users` table `avatar` column with the new URL.
4.  Return the new URL in the JSON response: `{ "url": "..." }`.

- Update the Inertia page prop `auth.user.avatar` or force a reload to reflect changes.

## Connected Accounts (Socialite)
The frontend now includes a "Connected Accounts" section in `Profile.vue` for GitHub and Google.

**Requirements:**
1.  **Install Socialite:** `composer require laravel/socialite`.
2.  **Configure Providers:** Set up `GITHUB_CLIENT_ID`, `GOOGLE_CLIENT_ID`, etc., in `.env` and `config/services.php`.
3.  **Database Migration:** Add columns to `users` table or create a `social_identities` table to store:
    - `provider_name` (github, google)
    - `provider_id`
    - `provider_token`
    - `avatar_url` (to sync profile photo)
4.  **Routes & Controllers:**
    - `GET /auth/{provider}/redirect`
    - `GET /auth/{provider}/callback`
5.  **Logic:**
    - On login/connect, check if user exists.
    - If user exists, link account.
    - If new user (registration), create account.
    - **Crucial:** If the user connects a provider, check if their avatar is default. If so, update `users.avatar` with the social provider's avatar URL.
6.  **API:** Expose connected providers status to the frontend (e.g., via `Inertia::share`) so the UI can toggle "Connect" vs "Connected".

## Authentication (Login with Social)
The login page has been updated with "Login with GitHub" and "Login with Google" buttons.

**Routes Required:**
- `GET /auth/github/redirect` -> Redirects to GitHub OAuth.
- `GET /auth/google/redirect` -> Redirects to Google OAuth.
- **Callback Handling:** These should handle both *login* (existing user) and *registration* (new user) flows.
- **Merge Logic:** If a user logs in with email `foo@bar.com` via Google, and an account with that email already exists (password-based), silently link them or prompt for password to verify linkage. Avoid creating duplicate accounts with the same email.

