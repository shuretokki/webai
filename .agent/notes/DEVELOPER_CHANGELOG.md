# Developer Changelog

**Purpose:** Detailed technical log of all code changes for development reference. Includes before/after comparisons, line numbers, and implementation details.

**Format:** Each entry documents WHO changed WHAT, WHERE, WHY, and HOW with code examples.

---

## [2025-12-15 23:25:00] - UI Refinements & Reasoning Support

### Summary
Reverted the delete modal to the standard centered style to align with other modals. Added a "Greeter" empty state to new chats for better UX. Implemented a "Reasoning Mode" accordion in the message component to separate `<think>` blocks from the final answer, specifically for Gemini 2.5 Flash reasoning capabilities.

### Why
- **Consistency:** Delete modal looked different from Edit modal.
- **Onboarding:** Empty chats looked broken; greeter provides context.
- **Readability:** Reasoning models output long thought chains; hiding them by default cleans up the chat.

### Files Changed

#### `resources/js/components/chat/Sidebar.vue` & `resources/js/pages/chat/Index.vue` (REVERTED)
- **Change:** Removed `width < 768` conditional props (`align`, `content-class`) from Delete Modal.
- **Why:** To enforce standard modal styling consistent with the Edit Modal.

#### `resources/js/pages/chat/Index.vue` (ADDED)
- **Feature:** Greeter component.
- **Logic:** `v-if="uiMessages.length === 0"`
- **Content:** "How can I help you today?" with star icon.

#### `resources/js/components/chat/Message.vue` (ENHANCED)
- **Feature:** Reasoning Accordion.
- **Logic:** content parsing for `<think>...</think>` tags.
- **UI:** `<details>` element with distinct styling for the thought process. Icon used is `i-solar-brain-linear`.
- **Impact:** Separates the "Reasoning chain" from the final markdown response.

### Code Snippet (Message Parsing)
```typescript
const reasoning = computed(() => {
    if (!props.content) return null;
    const match = props.content.match(/<think>([\s\S]*?)<\/think>/);
    return match ? match[1].trim() : null;
});
```

---

## [2025-12-15 22:30:00] - Settings Redesign & UI Polish

### Summary
Comprehensive redesign of the Settings Modal to match "Ecnelis" design specs (2-column layout, dark theme, squared corners). Fixed critical Vue/CSS issues including `props` reference errors and redundant transition properties. Improved Chat Input UX by disabling submission during streaming.

### Why
- **User Experience:** Settings modal was basic; needed premium feel.
- **Usability:** Users could double-submit messages while AI was typing.
- **Code Quality:** Console was spamming CSS warnings and JS errors.

### Files Changed

#### `resources/js/components/settings/SettingsModal.vue` (REDESIGNED)
- **Layout:** Conversion to `w-1/3` sidebar + `flex-1` content area.
- **Style:** Applied `rounded-none`, `bg-card/30`, `font-space`.
- **Features:** Added Account, Behavior (Toggles), Data Control sections.
- **Fix:** Removed redundant `transition transition-transform` classes (replaced with `transition-transform`).

#### `resources/js/components/chat/ChatInput.vue` (ENHANCED)
- **State:** Accepts `:is-streaming` prop.
- **Logic:** `:disabled="(!input.trim() && attachments.length === 0) || isStreaming"`
- **Why:** Prevents race conditions during active generation.

#### `resources/js/components/ui/Modal.vue` (FIXED)
- **Issue:** `defineProps` result wasn't assigned to variable.
- **Fix:** `const props = defineProps<{ ... }>()`.
- **Why:** Template was referencing `props.maxWidth` which failed at runtime.

---

## [2025-12-15 19:55:00] - Chat Frontend/Backend Alignment Fixes

### Summary
Fixed critical frontend/backend misalignments in chat functionality affecting SSE parsing, WebSocket message deduplication, attachment rendering, and error handling. All fixes follow production best practices and the Ecnelis design system.

### Why
Several issues were identified during backend research:
1. SSE `[Done]` parsing was fragile and could break with whitespace
2. Echo broadcasts were creating duplicate assistant messages
3. Attachments were commented out, making uploads invisible
4. Backend errors weren't shown to users
5. Storage symlink needed verification for file URLs

### How
1. Made SSE parsing robust with `.trim()` and dual termination support
2. Added deduplication check before pushing Echo messages
3. Uncommented attachments with proper Ecnelis styling
4. Added error display in chat UI with visual indicators
5. Verified storage symlink exists

---

### Files Changed

#### `resources/js/pages/chat/Index.vue` (ENHANCED - 3 fixes)

**Fix #1: Robust SSE [Done] Parsing**

**Location:** Lines 183-212

**Before:**
```javascript
const data = line.slice(6);
if (data === '[Done]')
    continue;
```

**After:**
```javascript
const data = line.slice(6).trim();
if (data === '[Done]' || data === '[DONE]')
    continue;
```

**Why:**
- `.trim()` removes any trailing/leading whitespace
- Support both `[Done]` and `[DONE]` for consistency
- More defensive against backend format variations

---

**Fix #2: Error Handling in SSE Stream**

**Location:** Lines 192-210

**Before:**
```javascript
try {
    const json = JSON.parse(data);
    if (!json.chat_id) {
        streaming.value += json.text;
    } else {
        window.history.replaceState({}, '', `/chat/${json.chat_id}`);
        form.chat_id = json.chat_id;
    }
} catch (e) {
    console.error('Error parsing JSON', e);
}
```

**After:**
```javascript
try {
    const json = JSON.parse(data);

    // Handle error response from backend
    if (json.error) {
        console.error('Stream error:', json.error);
        streaming.value += '\n\n⚠️ Error: ' + json.error;
        continue;
    }

    if (!json.chat_id) {
        streaming.value += json.text;
    } else {
        window.history.replaceState({}, '', `/chat/${json.chat_id}`);
        form.chat_id = json.chat_id;
    }
} catch (e) {
    console.error('Error parsing JSON', e);
}
```

**Why:**
- Backend sends `data: {"error": "..."}` for quota/model errors (see ChatController.php:230)
- Users need to see these errors in UI, not just console
- Uses ⚠️ emoji for visual clarity
- Continues streaming after error (doesn't break flow)

---

**Fix #3: Echo Message Deduplication**

**Location:** Lines 230-252

**Before:**
```javascript
useEcho(
    `chats.${props.chatId}`,
    '.message.sent',
    (event: any) => {
        if (event.message && event.message.role === 'assistant') {
            props.messages.push({
                role: event.message.role,
                content: event.message.content,
                attachments: []
            });
            scrollToBottom();
        }
    },
    [],
    'private'
);
```

**After:**
```javascript
useEcho(
    `chats.${props.chatId}`,
    '.message.sent',
    (event: any) => {
        if (event.message && event.message.role === 'assistant') {
            // Deduplicate: only add if not already present from streaming
            const isDuplicate = props.messages.some(
                m => m.role === 'assistant' && m.content === event.message.content
            );

            if (!isDuplicate) {
                props.messages.push({
                    role: event.message.role,
                    content: event.message.content,
                    attachments: []
                });
                scrollToBottom();
            }
        }
    },
    [],
    'private'
);
```

**Why:**
- SSE streams responses to the sender
- Echo broadcasts completion to all devices
- Without deduplication, sender sees message twice
- Checks if content already exists before pushing

**Edge Cases Handled:**
- User streaming on Tab A → message already added via SSE
- Echo broadcast arrives → deduplication prevents duplicate
- Other user on Tab B → no streaming, Echo adds correctly

---

#### `resources/js/components/chat/Message.vue` (FIXED)

**Purpose:** Enable attachment rendering with production design

**Location:** Lines 65-80

**Before:**
```vue
<!-- <div v-if="attachments?.length" class="flex flex-wrap gap-2 mt-2">
    <div v-for="(att, i) in attachments" :key="i" class="relative group">
        <img v-if="att.type === 'image'" :src="att.url" class="h-32 w-auto rounded-lg border border-white/10" />
        <a v-else :href="att.url" target="_blank"
            class="flex items-center gap-2 bg-white/5 p-2 rounded-lg border border-white/10 hover:bg-white/10">
            <i-solar-file-text-linear class="text-xl" />
            <span class="text-xs">{{ att.name || 'File' }}</span>
        </a>
    </div>
</div> -->
```

**After:**
```vue
<div v-if="attachments?.length" class="flex flex-wrap gap-3 mt-3">
    <div v-for="(att, i) in attachments" :key="i" class="relative group">
        <img v-if="att.type === 'image'"
            :src="att.url"
            :alt="att.name || 'Image'"
            class="h-40 w-auto rounded-none border border-white/10 object-cover hover:border-primary/50 transition-colors" />
        <a v-else
            :href="att.url"
            target="_blank"
            class="flex items-center gap-2 bg-white/5 px-3 py-2 rounded-none border border-white/10 hover:bg-white/10 hover:border-primary/50 transition-colors">
            <i-solar-file-text-linear class="text-xl text-primary" />
            <span class="text-sm font-space">{{ att.name || 'File' }}</span>
        </a>
    </div>
</div>
```

**Design System Compliance:**
- `rounded-none` → Squared corners (Ecnelis guideline)
- `gap-3`, `px-3`, `py-2` → Design tokens (no arbitrary values)
- `font-space` → Space Grotesk for body text
- `text-primary` → Uses CSS variable from design system
- `h-40` → Consistent image sizing (was h-32)
- `hover:border-primary/50` → Interactive feedback

**Backend Integration:**
- Works with `AttachmentResource` structure: `{type, url, name}`
- `url` comes from `asset('storage/...')` in backend
- Displays images with preview, files with icon + name

---

#### Storage Symlink Verification

**Command:**
```bash
php artisan storage:link
```

**Output:**
```
INFO  The [public/storage] link has been connected to [storage/app/public].
```

**Why Needed:**
- `AttachmentResource` uses `asset('storage/attachments/...')`
- Without symlink, URLs return 404
- Laravel stores uploads in `storage/app/public`
- Symlink makes them accessible at `public/storage`

---

### Testing Plan

**Manual Tests:**
1. ✓ Upload image → Verify renders in chat with preview
2. ✓ Upload PDF → Verify shows file icon with name
3. ✓ Send message → Verify no duplicate from Echo
4. ✓ Exceed quota → Verify error shows in chat UI
5. ✓ Stream response → Verify [Done] terminates correctly

**Automated Tests:**
- Backend: Will run `php artisan test` (existing 69 tests)
- Frontend: Will run `vitest` if configured

---

### Production Best Practices Applied

1. **Defensive Programming:** Added `.trim()` and dual format support
2. **User Experience:** Errors shown in UI, not just console
3. **Edge Case Handling:** Deduplication logic for Echo conflicts
4. **Design Consistency:** Follows Ecnelis design system exactly
5. **Accessibility:** Added alt text to images, semantic HTML
6. **Performance:** Deduplication uses `.some()` (O(n) acceptable for chat)

---

## [2025-12-15 22:00:00] - Security Audit & Authorization Cleanup
(Truncated previous entries for brevity)
