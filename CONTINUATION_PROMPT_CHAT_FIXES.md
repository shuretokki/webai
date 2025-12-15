# Chat Frontend/Backend Alignment - Continuation Prompt

## Context
This continuation is for fixing all identified problems in the Chat frontend after a comprehensive backend research phase. The backend analysis has been completed and specific discrepancies have been identified.

## What Was Already Done
1. ✅ **UI Refinements**: "Ecnelis" branding applied (squared corners, Philosopher font, specific logo behavior)
2. ✅ **Settings Modal**: Created and teleported to body
3. ✅ **Header Actions**: Moved Edit/Delete from Sidebar to Header Dropdown in `Index.vue`
4. ✅ **Bug Fixes**: Resolved Vue warnings (`inheritAttrs` in Sidebar, `AnimatePresence` import)
5. ✅ **Backend Research**: Analyzed `ChatController`, `MessageResource`, `AttachmentResource`, Routes, and all frontend components

## Current State Analysis

### Backend (`ChatController.php`)
- **SSE Format**: Sends `data: {"chat_id": ...}` first, then `data: {"text": "..."}` chunks, and finally `data: [Done]` (exact string, no JSON)
- **Attachments**: Stored via `AttachmentResource` which returns `{type: 'image'|'file', url: asset('storage/...'), name: '...'}`
- **Models**: Config includes `is_free` flag. Non-free models show demo/simulated responses
- **Echo Event**: Broadcasts `MessageSent` event to `chats.{id}` channel

### Frontend Issues Identified

#### 1. **`Index.vue` SSE Parsing** (Line 188-210)
**Problem**: The `[Done]` check is fragile:
```javascript
if (data === '[Done]')
    continue;
```
This may fail if there's trailing whitespace or the backend changes format slightly.

**Fix Needed**:
```javascript
if (data.trim() === '[DONE]' || data === '[Done]')
    continue;
```

#### 2. **Message Duplication Risk** (Line 230-245)
**Problem**: Echo listener pushes assistant messages unconditionally. If the user is actively streaming, the broadcasted message will duplicate the streamed content.

**Fix Needed**: Add a deduplication check:
```javascript
if (event.message && event.message.role === 'assistant') {
    // Only add if not already present (deduplicate)
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
```

#### 3. **`Message.vue` Attachments** (Line 73-82)
**Problem**: Attachment rendering is commented out. Users can upload files but can't see them in the chat.

**Fix Needed**:
- Uncomment the attachment section
- Apply "Ecnelis" design (squared corners, proper spacing)
- Match the structure from `AttachmentResource` (uses `type`, `url`, `name`)

**Updated Code**:
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

#### 4. **Storage Symlink Verification**
**Problem**: `AttachmentResource` uses `asset('storage/...')` which requires the storage symlink to exist.

**Action Needed**: Run `php artisan storage:link` to ensure uploaded files are accessible.

#### 5. **Error Handling in SSE**
**Problem**: The backend can send `data: {"error": "..."}` but the frontend doesn't handle it visually.

**Fix Needed** (in `Index.vue`, Line 195-210):
```javascript
try {
    const json = JSON.parse(data);
    if (json.error) {
        // Show error to user
        console.error('Stream error:', json.error);
        streaming.value += '\n\n⚠️ Error: ' + json.error;
        continue;
    }
    if (!json.chat_id) {
        streaming.value += json.text;
    } else {
        window.history.replaceState(
            {}, '', `/chat/${json.chat_id}`);
        form.chat_id = json.chat_id;
    }
} catch (e) {
    console.error('Error parsing JSON', e);
}
```

## Files to Edit

### Priority 1 (Critical)
1. **`resources/js/pages/chat/Index.vue`**
   - Fix SSE `[Done]` parsing (Line 193)
   - Add error handling for `json.error` (Line 195-210)
   - Deduplicate Echo messages (Line 235-242)

2. **`resources/js/components/chat/Message.vue`**
   - Uncomment and style attachments section (Line 73-82)
   - Apply squared design (`rounded-none`)
   - Use correct prop structure (`att.url`, `att.name`, `att.type`)

### Priority 2 (Important)
3. **Run Terminal Command**
   - `php artisan storage:link` (ensure storage symlink exists)

4. **Test the Flow**
   - Upload an image → Verify it renders in chat
   - Send a message → Verify no duplication from Echo
   - Check console for SSE parsing errors

## Design System Reminder
- **Squared Corners**: Use `rounded-none` everywhere (no `rounded-lg`, `rounded-md`)
- **Font**: Philosopher for headings/branding, Space Grotesk for body
- **Colors**: Primary (`#dbf156`), Background (`#1e1e1e`), Borders (`white/10`)
- **Spacing**: Use `gap-3`, `px-4`, `py-2` (design tokens, no arbitrary values)

## Testing Checklist
- [ ] Upload image → Appears in chat with preview
- [ ] Upload PDF → Shows file icon with name
- [ ] Send message → No duplicate from Echo broadcast
- [ ] Stream response → Parses `[Done]` correctly
- [ ] Error from backend → Displays in chat UI
- [ ] New chat → History updates without page refresh

## Next Steps
1. Apply all fixes using `multi_replace_string_in_file` for efficiency
2. Run `php artisan storage:link`
3. Test file upload flow end-to-end
4. Verify no console errors during streaming
5. Check Echo connection in browser DevTools Network tab

## Technical Stack Reference
- **Backend**: Laravel 12, Inertia v2, Prism (AI), SSE
- **Frontend**: Vue 3 (Composition API), Tailwind v4, Motion-v
- **Icons**: Solar Icons via `unplugin-icons` (e.g., `<i-solar-pen-linear />`)
- **Design**: "Ecnelis" theme (Philosopher + Space Grotesk, squared, dark mode)

---

**Start Point**: Apply the 5 fixes listed above, then test the complete chat flow.
