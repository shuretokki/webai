# CHANGELOG

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

---

## [2025-12-16 02:00:00] - Social Login & Login Redesign

### Added
- **Authentication:** Added "Login with GitHub" and "Login with Google" buttons to the valid login page.
- **Documentation:** Updated backend notes with Socialite integration requirements.

### Changed
- **UI:** Redesigned the Login page to match the application's premium "Ecnelis" aesthetic (Dark mode, squared inputs).

---

## [2025-12-16 01:40:00] - Icon System Migration

### Changed
- **System-wide:** Replaced all Solar icons with **Lucide Icons** for a cleaner, more consistent UI usage.
- **Files:** Updated `Sidebar.vue`, `Index.vue`, `ChatInput.vue`, `Message.vue`, `SettingsModal.vue`, `SettingsContent.vue`, `Usage.vue`, and `SearchModal.vue`.
- **Reasoning:** Lucide provides a more professional, cohesive visual style aligned with the application's premium aesthetic.

---

## [2025-12-15 23:25:00] - UI Refinements & Reasoning Support

### Added
- **File:** `resources/js/pages/chat/Index.vue`
- **Description:** Added a "Greeter" (Empty State) component that appears when a chat has no messages.
- **Impact:** improves user onboarding and empty state aesthetics.

- **File:** `resources/js/components/chat/Message.vue`
- **Description:** Added support for "Reasoning Mode" (for Gemini 2.5 Flash). Automatically parses `<think>` tags and displays the reasoning process in a collapsible accordion details element. Note: Used `i-solar-stars-minimalistic-linear` for the icon.
- **Impact:** Cleaner chat interface by separating intermediate reasoning thoughts from the final answer.

### Changed
- **File:** `resources/js/components/chat/Sidebar.vue` & `resources/js/pages/chat/Index.vue`
- **Description:** Reverted the "Delete Chat" modal to the standard centered design (removing bottom-sheet styling on mobile) to ensure consistency with the "Edit Chat" modal.
- **Impact:** Consistent modal experience across the application.

---

## [2025-12-15 19:55:00] - Chat Frontend/Backend Alignment Fixes

### Fixed
- **File:** `resources/js/components/chat/ChatInput.vue`
- **Description:** Disabled chat input submit button while AI is streaming response.
- **Impact:** Prevents duplicate message submissions and UI confusion during streaming.

### Added
- **File:** `resources/js/components/settings/SettingsModal.vue`
- **Description:** Redesigned Settings Modal with 2-column "Ecnelis" layout (Sidebar + Content), dark theme, and squared corners.
- **Impact:** Premium, cohesive design aligning with the app's aesthetic.

- **File:** `resources/js/components/settings/SettingsModal.vue`
- **Description:** Added "Data Usage" link under Data Control section.
- **Impact:** Users can easily access their usage statistics from settings.

### Fixed
- **File:** `resources/js/components/settings/SettingsModal.vue`
- **Description:** Fixed CSS transition warnings ("redundant transition properties") in toggle switches.
- **Impact:** Clean console output and smoother animations.

- **File:** `resources/js/components/ui/Modal.vue`
- **Description:** Fixed `props is not defined` reference error.
- **Impact:** Modals now initialize correctly without runtime errors.

---

## [2025-12-15 19:55:00] - Chat Frontend/Backend Alignment Fixes

- **File:** `resources/js/pages/chat/Index.vue`
- **Description:** Added error handling for backend error responses in SSE stream. Errors are now displayed directly in chat UI with ⚠️ emoji prefix.
- **Impact:** Users can see backend errors (quota exceeded, model errors) directly in chat instead of only in console.

- **File:** `resources/js/pages/chat/Index.vue`
- **Description:** Implemented message deduplication logic in Echo WebSocket listener to prevent duplicate assistant messages.
- **Impact:** Echo broadcasts no longer create duplicate messages when user is actively streaming responses.

- **File:** `resources/js/components/chat/Message.vue`
- **Description:** Uncommented and restyled attachment rendering section with Ecnelis design system (squared corners, proper spacing, design tokens).
- **Impact:** Users can now see uploaded images and files in their chat messages.

### Changed
- **Command:** `php artisan storage:link`
- **Description:** Verified storage symlink exists to ensure uploaded files are accessible.
- **Impact:** Attachment URLs using `asset('storage/...')` now resolve correctly.

### Why
Frontend and backend had several misalignments causing issues with streaming, attachments, and real-time updates. These fixes ensure production-ready chat experience with proper error handling and visual feedback.

---

## [2025-12-15 22:00:00] - Security Audit & Authorization Improvements
(Truncated previous entries)
