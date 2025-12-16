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

## User Settings & Security
The frontend `Profile.vue` uses `ProfileController` and `Form` components which imply a standard form submission flow.

**Immediate Backend Requirements:**
1.  **Profile Update (Name/Email):**
    -   Ensure standard Fortify/Jetstream routes (e.g., `PUT /user/profile-information`) are active and mapped correctly.
    -   The frontend connects to `ProfileController.update`. Verify this matches your route naming convention or update the frontend to use standard routes (e.g., `/user/profile-information`).
2.  **Email Verification:**
    -   The frontend has UI for "Resend Verification".
    -   Ensure `POST /email/verification-notification` is implemented/active (Fortify default).
3.  **Two-Factor Authentication (2FA):**
    -   User requested "make 2fa work".
    -   Ensure Fortify's 2FA features are enabled in `config/fortify.php`.
    -   Frontend needs endpoints for:
        -   Enabling 2FA (`POST /user/two-factor-authentication`)
        -   Getting QR Code (`GET /user/two-factor-qr-code`)
        -   Confirming 2FA with OTP (`POST /user/confirmed-two-factor-authentication`)
        -   Getting Recovery Codes (`GET /user/two-factor-recovery-codes`)

## Content Pages (Docs & Blog)
Frontend currently uses **mock static pages** (`Docs.vue`, `Blog.vue`).

**Future Backend Requirements:**
1.  **Documentation:**
    -   If docs should be dynamic, create a `DocumentationController` that reads markdown files or from a CMS.
    -   Pass content as Inertia props to `Docs.vue`.
2.  **Blog:**
    -   If blog posts are managed in DB, create `Post` model and `BlogController`.
    -   Pass paginated posts to `Blog.vue`.

