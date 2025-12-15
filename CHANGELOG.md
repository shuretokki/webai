# CHANGELOG

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

---

## [2025-12-15 16:15:00] - Expanded Model Support

### Changed
- **File:** `config/ai.php`
- **Description:** Updated with comprehensive list of models from OpenAI, Anthropic, Gemini, Mistral, Groq, xAI, DeepSeek, and Ollama.
- **Impact:** Supports a wider range of AI models and providers.

- **File:** `app/Http/Controllers/ChatController.php`
- **Description:** Updated to handle new providers and model selection logic.
- **Impact:** Backend now supports additional AI models and providers.

- **File:** `resources/js/components/chat/ChatInput.vue`
- **Description:** Updated to display models in a "mega dropdown" grouped by provider.
- **Impact:** Improved user interface for selecting AI models.

- **File:** `resources/js/components/chat/UpgradePlanModal.vue`
- **Description:** Modal component prompting users to upgrade plan for restricted models.
- **Impact:** Guides users to appropriate subscription plans based on model access.

- **File:** `resources/js/pages/chat/Index.vue`
- **Description:** Integrated model selector and upgrade plan modal.
- **Impact:** Unified interface for chat and model management.

### Why
- **Feature Request:** Users requested ability to choose between different AI models (e.g., GPT-3.5, GPT-4).
- **Flexibility:** Different models have different capabilities and costs; users should control this.
- **Future-Proofing:** Easily add new models or change pricing in the future.

---

## [2025-12-15 16:00:00] - Multi-Model Support

### Added
- **File:** `config/ai.php`
- **Description:** New configuration file for AI model settings.
- **Impact:** Allows dynamic selection of AI models and pricing.

- **File:** `app/Models/UserUsage.php`
- **Description:** Updated to calculate costs based on selected AI model.
- **Impact:** Accurate usage tracking and billing for different AI models.

- **File:** `resources/js/components/chat/ChatInput.vue`
- **Description:** Added model selector dropdown for choosing AI model.
- **Impact:** Users can select desired AI model per conversation.

- **File:** `resources/js/components/chat/UpgradePlanModal.vue`
- **Description:** Modal component prompting users to upgrade plan for restricted models.
- **Impact:** Guides users to appropriate subscription plans based on model access.

- **File:** `resources/js/pages/chat/Index.vue`
- **Description:** Integrated model selector and upgrade plan modal.
- **Impact:** Unified interface for chat and model management.

### Changed
- **File:** `app/Http/Controllers/ChatController.php`
- **Description:** Modified to handle multiple AI models for responses.
- **Impact:** Backend now supports dynamic AI model usage per request.

- **File:** `routes/web.php`
- **Description:** Updated routes to reflect new model-based architecture.
- **Impact:** Ensures correct handling of requests for different AI models.

### Why
- **Feature Request:** Users requested ability to choose between different AI models (e.g., GPT-3.5, GPT-4).
- **Flexibility:** Different models have different capabilities and costs; users should control this.
- **Future-Proofing:** Easily add new models or change pricing in the future.

---

## [2025-12-15 15:15:00] - Frontend Search UI & WebSocket Fixes

### Added
- **File:** `resources/js/components/chat/SearchModal.vue`
- **Description:** New Command Palette-style search modal with keyboard navigation, debouncing, and result highlighting.
- **Impact:** Allows users to quickly find chats via `Cmd/Ctrl+K`.

### Changed
- **File:** `resources/js/pages/chat/Index.vue`
- **Description:** Integrated `SearchModal`, added global keyboard shortcut listener, and fixed WebSocket listener event name.
- **Impact:** Search is accessible from chat interface; Real-time updates now work correctly with `.message.sent` event name.

- **File:** `app/Events/MessageSent.php`
- **Description:** Added `broadcastAs()` method to return `message.sent`.
- **Impact:** Ensures consistent event naming for frontend listener, avoiding namespace issues.

- **File:** `routes/web.php`
- **Description:** Removed temporary test routes (`/test-reverb`).
- **Impact:** Cleanup of debug code.

### Removed
- **Files:** `resources/js/pages/TestReverb.vue`, `app/Events/TestEvent.php`
- **Reason:** WebSocket functionality verified and debugged; test files no longer needed.

### Why
- **Search UI:** Improves navigation and user experience for finding past conversations.
- **WebSocket Fixes:** Debugging revealed that `broadcastAs()` was needed for reliable event listening in Laravel Echo.

---

## [2025-12-15 14:30:00] - Real-time Updates (Frontend Complete)

### Changed
- **File:** `resources/js/pages/chat/Index.vue`
- **Lines:** 163-187
- **Before:** No WebSocket listener, messages only visible on sender's device via SSE
- **After:** Added `useEcho()` hook to subscribe to `chats.{chatId}` private channel and listen for `MessageSent` events
- **Impact:** Messages now sync instantly across all devices viewing the same chat

**Implementation:**
```typescript
// Subscribe to private channel when chat loads
const echoControl = useEcho(
    `chats.${props.chatId}`,
    'MessageSent',
    (event) => {
        props.messages.push({
            role: event.message.role,
            content: event.message.content,
            attachments: []
        });
        scrollToBottom();
    }
);

// Cleanup on unmount
onUnmounted(() => {
    if (echoControl) {
        echoControl.leaveChannel();
    }
});
```

**Why:**
- Enables ChatGPT-like multi-device experience
- Uses Laravel Echo's Vue composable for automatic channel management
- Backend was already broadcasting (completed 2025-12-15 12:11:21)
- Frontend listener is the final piece to complete real-time feature

**Testing:**
1. Open chat in Tab A
2. Open same chat in Tab B
3. Send message from Tab A
4. **Expected:** Message appears in Tab B instantly without refresh

---

## [2025-12-15 12:11:21] - Real-time Updates (Backend)

### Added
- **File:** `app/Events/MessageSent.php`
- **Lines:** 1-62 (new file)
- **What:** WebSocket broadcasting event for real-time message sync
- **Impact:** Messages appear instantly across devices without page refresh

**Implementation:**
- Created `MessageSent` event implementing `ShouldBroadcast`
- Broadcasts to private channel `chats.{id}` after AI completes response
- Includes message data (id, role, content, timestamp) in payload

### Added
- **File:** `routes/channels.php`
- **Lines:** 14-21
- **What:** Channel authorization for WebSocket security
- **Impact:** Only chat owners can subscribe to their chat channels

**Authorization Logic:**
```php
Broadcast::channel('chats.{chatId}', function ($user, $chatId) {
    $chat = Chat::find($chatId);
    return $chat && $chat->user_id === $user->id;
});
```
Prevents users from listening to other users' private conversations.

### Changed
- **File:** `app/Http/Controllers/ChatController.php`
- **Lines:** 179-183
- **Before:** `$chat->messages()->create([...])`
- **After:** `$assistantMessage = $chat->messages()->create([...]); event(new \App\Events\MessageSent($chat, $assistantMessage));`
- **Impact:** Triggers WebSocket broadcast after each AI response

### Added
- **File:** `config/broadcasting.php` (auto-generated)
- **Lines:** 1-83
- **What:** Broadcasting configuration for Reverb/Pusher drivers
- **Impact:** Enables WebSocket infrastructure in Laravel

### Added
- **File:** `package.json`
- **Lines:** 27-29
- **What:** Installed `laravel-echo` (v2.2.6) and `pusher-js` for WebSocket client
- **Impact:** Frontend can now connect to WebSocket server

### Changed
- **File:** `resources/js/app.ts`
- **Lines:** 8-12
**Before:** No Echo configuration
- **After:** Added `configureEcho({ broadcaster: 'reverb' })`
- **Impact:** Laravel Echo initialized globally for Vue app

### Why
Implement ChatGPT-like real-time experience. SSE (Server-Sent Events) streams AI responses to the sender, WebSocket broadcasts completion to other devices. Both technologies coexist perfectly.

**Status:** Backend complete ✅ | Frontend listener pending ⏳

---

## [2025-12-15 09:26:10] - Testing Suite Completion

### Added
- **File:** `tests/Feature/SearchTest.php`
- **Lines:** 1-209 (7 test cases, 29 assertions)
- **What:** Comprehensive test coverage for search API endpoint
- **Impact:** 100% test pass rate (66/66), improved code quality and reliability

**Test Coverage:**
1. **Empty Results Test**: Verifies API returns empty array for non-matching queries
2. **Chat Title Search**: Tests LIKE-based search across chat titles with multiple matches
3. **Message Content Search**: Tests full-text search within message content
4. **Result Limiting**: Validates 15-item limit (max 5 chats + 10 messages)
5. **SQL Injection Prevention**: Confirms `%` and `_` are escaped and don't act as wildcards
6. **Multi-user Isolation**: Security test ensuring users only see their own data
7. **Authentication**: Confirms unauthenticated requests return 401

### Fixed
- **File:** `database/factories/UserFactory.php`
- **Lines:** 47-56
- **Before:** Missing `withoutTwoFactor()` method caused 2 auth tests to fail
- **After:** Added no-op `withoutTwoFactor()` method for Breeze/Jetstream compatibility
- **Impact:** All Laravel Breeze authentication tests now pass (was 57/59, now 66/66)

**Details:**
- This method is called by Laravel's default auth test suite
- Returns `$this` unchanged (app doesn't use 2FA)
- Follows Laravel Jetstream pattern for test compatibility

### Why
Production-ready applications require comprehensive test coverage. The search API handles user data and SQL queries - critical areas that need automated verification. Achieving 100% test pass rate provides confidence for deployment and future refactoring.

---

## [2025-12-15] - Search Backend API

### Added
- **File:** `app/Http/Controllers/ChatController.php`
- **Lines:** 233-301
- **What:** Added `search()` method for searching chats and messages
- **Impact:** Users can now search their chat history via API endpoint

**Details:**
- LIKE-based search across chat titles and message content
- Returns max 15 results (5 chats + 10 messages)
- Escapes special characters (`%`, `_`) to prevent SQL injection
- Filters results to current user only
- Returns structured JSON with type, title, URL, subtitle

### Changed
- **File:** `routes/web.php`
- **Lines:** 40-42
- **Before:** No search route
- **After:** Added `Route::get('/chat/search', [ChatController::class, 'search'])->name('chat.search')`
- **Impact:** Search endpoint is now accessible at `/chat/search?q={query}`

### Why
Enables users to find specific conversations quickly without scrolling through history. Foundation for command palette (Cmd+K) feature.

---

## [2025-12-14] - Filament Admin Panel

### Added
- **File:** `app/Providers/Filament/AdminPanelProvider.php`
- **Lines:** 1-59
- **What:** Configured Filament admin panel with authentication and middleware
- **Impact:** Admin users can access `/admin` dashboard

**Resources Created:**
1. `UserResource.php` - Manage users, subscription tiers, admin status
2. `ChatResource.php` - Monitor chats, view message counts, soft delete management
3. `UserUsageResource.php` - Analytics dashboard with cost summaries

### Added
- **File:** `database/migrations/2025_12_14_155234_add_is_admin_to_users_table.php`
- **Lines:** 1-31
- **What:** Added `is_admin` boolean column to `users` table
- **Impact:** Users can be designated as administrators

### Added
- **File:** `app/Http/Middleware/IsAdmin.php`
- **Lines:** 1-26
- **What:** Created middleware to protect admin routes
- **Impact:** Only users with `is_admin = true` can access admin panel

### Changed
- **File:** `app/Models/User.php`
- **Line:** 27
- **Before:** `$fillable` did not include `is_admin`
- **After:** Added `'is_admin'` to `$fillable` array
- **Impact:** Admin status can be mass-assigned

### Why
Admins need a way to manage users, monitor usage, and handle support issues without writing custom pages. Filament provides 95% of this for free.

---

## [2025-12-14] - Authorization Policies

### Added
- **File:** `app/Policies/ChatPolicy.php`
- **Lines:** 1-65
- **What:** Created policy with view, update, delete, restore, forceDelete methods
- **Impact:** Users can only access their own chats, unauthorized access returns 403

### Changed
- **File:** `routes/web.php`
- **Lines:** 23-31
- **Before:** Routes used query parameter `/chat?chat_id=1`
- **After:** Routes use route model binding `/chat/{chat}`
- **Impact:** RESTful URLs, automatic 404s, cleaner architecture

**Routes Updated:**
- `DELETE /chat/{chat}` with `->can('delete', 'chat')`
- `PATCH /chat/{chat}` with `->can('update', 'chat')`
- `GET /chat/{chat?}` with optional route binding

### Changed
- **File:** `app/Http/Controllers/ChatController.php`
- **Line:** 18
- **Before:** `public function index(Request $request)`
- **After:** `public function index(Request $request, ?Chat $chat = null)`
- **Impact:** Automatic model loading, no manual `Chat::find()` needed

### Changed
- **File:** `resources/js/components/chat/Sidebar.vue`
- **Line:** 135
- **Before:** `Chat({ query: { chat_id: chat.id } }).url`
- **After:** `Chat({ chat: chat.id }).url`
- **Impact:** Frontend uses RESTful URLs, better SEO

### Why
Prevents unauthorized access to other users' chats. Route model binding reduces code and auto-handles 404s. RESTful URLs are semantic and cacheable.

---

## [2025-12-14] - Usage Tracking System

### Added
- **File:** `app/Models/UserUsage.php`
- **Lines:** 1-37
- **What:** Model for tracking user consumption (messages, tokens, bytes, cost)
- **Impact:** Can track and bill users accurately

**Key Features:**
- Static `record()` helper for easy tracking
- Supports metadata JSON for extra context
- Timestamps for time-series analysis

### Added
- **File:** `app/Models/User.php`
- **Lines:** 65-86
- **What:** Added `currentMonthUsage()` and `hasExceededQuota()` methods
- **Impact:** Can check quotas in real-time and block over-limit users

### Added
- **File:** `app/Http/Controllers/ChatController.php`
- **Lines:** 108-119, 187-196
- **What:** Usage tracking on message send and AI response
- **Impact:** Every message and response is tracked for billing

### Added
- **File:** `resources/js/pages/settings/Usage.vue`
- **Lines:** 1-196
- **What:** Frontend dashboard showing usage stats
- **Impact:** Users can see their consumption and quota status

### Why
Required for SaaS billing, quota enforcement, and user transparency. Users need to know when they're approaching limits.

---

## [2025-12-14] - Rate Limiting

### Changed
- **File:** `app/Providers/AppServiceProvider.php`
- **Lines:** 34-42
- **What:** Configured two rate limiters (api, chat-messages)
- **Impact:** Prevents abuse and controls AI API costs

**Limits:**
- `api`: 60 requests/minute per IP
- `chat-messages`: 2 messages/minute per user

### Changed
- **File:** `routes/web.php`
- **Line:** 28-29
- **Before:** No rate limiting on chat stream
- **After:** Added `->middleware('throttle:chat-messages')`
- **Impact:** Users limited to 2 AI messages per minute

### Why
Prevents users from accidentally (or intentionally) racking up huge AI bills. Also reduces server load from spam/bots.

---

## [2025-12-14] - File Cleanup System

### Added
- **File:** `app/Observers/AttachmentObserver.php`
- **Lines:** 1-25
- **What:** Auto-deletes files from storage when attachment is deleted
- **Impact:** No orphaned files, storage costs controlled

### Added
- **File:** `app/Console/Commands/CleanupOldAttachments.php`
- **Lines:** 1-45
- **What:** Scheduled command to permanently delete soft-deleted attachments after 30 days
- **Impact:** Automated cleanup, GDPR compliance

### Changed
- **File:** `app/Models/Chat.php`
- **Lines:** 17-35
- **What:** Added `forceDeleting` event to cascade delete files
- **Impact:** When chat is permanently deleted, all associated files are removed

### Why
Storage costs money. Orphaned files waste space. Users expect deleted data to eventually be purged (GDPR).

---

## [2025-12-14] - Bug Fixes

### Fixed
- **File:** `app/Http/Requests/ChatRequest.php`
- **Line:** 14
- **Before:** `return false;` (blocked all requests!)
- **After:** `return auth()->check();`
- **Impact:** Chat streaming now works for authenticated users

**Symptom:** Users got "403 Forbidden" when sending messages
**Cause:** FormRequest's `authorize()` always returned false
**Fix:** Changed to check authentication status

### Fixed
- **File:** `resources/js/pages/chat/Index.vue`
- **Lines:** 129-130
- **Before:** Single quotes `'/chat/${json.chat_id}'` (no interpolation!)
- **After:** Backticks `` `/chat/${json.chat_id}` ``
- **Impact:** URL updates correctly when chat is created

**Symptom:** New chats would navigate to literal `/chat/${json.chat_id}`
**Cause:** JavaScript template literals require backticks, not single quotes
**Fix:** Changed to backticks for string interpolation

### Fixed
- **File:** `resources/js/pages/settings/Usage.vue`
- **Line:** 2
- **Before:** Missing `import { ref, computed, onMounted, onUnmounted } from 'vue';`
- **After:** Added Vue Composition API imports
- **Impact:** Usage dashboard loads without errors

**Symptom:** Page failed to load, undefined references
**Cause:** Auto-imports weren't configured, manual imports required
**Fix:** Added explicit imports for Vue composables

---

## [2025-12-15 15:30:00] - Export Functionality

### Added
- **File:** `app/Http/Controllers/ChatController.php`
- **Description:** Added `export` method to handle PDF and Markdown export requests.
- **Impact:** Users can now download their chat history.

- **File:** `resources/views/chat/export.blade.php`
- **Description:** Blade template for PDF generation with styling for user/assistant messages.
- **Impact:** Provides a clean, readable layout for exported PDFs.

- **File:** `resources/js/pages/chat/Index.vue`
- **Description:** Added a dropdown menu to the chat header with "Export as PDF" and "Export as Markdown" options.
- **Impact:** UI access to the export feature.

### Changed
- **File:** `routes/web.php`
- **Description:** Added `/chat/{chat}/export/{format}` route.
- **Impact:** Enables the export endpoint.

### Why
- **User Request:** Users want to save their conversations for offline reference or sharing.
- **Portability:** Markdown is great for developers; PDF is great for general users.

---

## Template for Future Entries

```markdown
## [YYYY-MM-DD] - Feature/Fix Name

### Added/Changed/Fixed/Removed
- **File:** `path/to/file.php`
- **Lines:** 45-62
- **Before:** What it was (if changed)
- **After:** What it is now
- **Impact:** Effect on users/performance/security

### Why
Brief rationale for the change.
```

---

**Last Updated:** 2025-12-15
**Total Changes:** 9 major features, 3 bug fixes
**Lines of Code:** ~1,130 backend + ~400 frontend
