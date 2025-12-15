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

**Frontend Update Needed:**
- Update `handleAvatarChange` in `Profile.vue` to make the actual axios/fetch call.
- Update the Inertia page prop `auth.user.avatar` or force a reload to reflect changes.
