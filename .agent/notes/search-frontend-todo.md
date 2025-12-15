# Search Feature - Frontend Implementation Note

## 📅 Backend Completed: 2025-12-15

---

## Backend API (✅ Done)

**Endpoint:** `GET /chat/search?q={query}`

**Response:**
```json
{
  "results": [
    {
      "type": "chat",
      "id": 1,
      "title": "My Chat Title",
      "url": "/chat/1",
      "subtitle": "2 hours ago"
    },
    {
      "type": "message",
      "id": 5,
      "title": "Hello, how are you?",
      "url": "/chat/1",
      "subtitle": "in My Chat Title"
    }
  ]
}
```

**Features:**
- Searches chat titles and message content
- LIKE-based search (case-insensitive)
- Escapes special characters (`%`, `_`)
- Returns max 15 results (5 chats + 10 messages)
- Only searches user's own data

---

## Frontend TODO

### **Option 1: Command Palette (Cmd+K)** ⭐ Recommended

**Component:** `resources/js/components/CommandPalette.vue`

**Features:**
- Press `Cmd+K` (Mac) or `Ctrl+K` (Windows) to open
- Fuzzy search with instant results
- Keyboard navigation (↑ ↓ Enter Esc)
- Search highlighting
- Beautiful modal overlay

**UX Flow:**
```
User → Cmd+K → Modal opens → Type "hello" → See results → Enter → Navigate to chat
```

**Tech Stack:**
- `@headlessui/vue` for accessible modal
- `fuse.js` for fuzzy search (client-side enhancement)
- Vue Composition API with refs

**Implementation Time:** 30 minutes

**Mockup:**
```
┌──────────────────────────────────────┐
│  ⌘ + K to search...                 │
├──────────────────────────────────────┤
│  📄 My Chat Title                    │
│      2 hours ago                     │
│                                      │
│  💬 Hello, how are you?              │
│      in My Chat Title                │
└──────────────────────────────────────┘
```

---

### **Option 2: Sidebar Search Bar** (Simple)

**Location:** Top of `Sidebar.vue`

**Features:**
- Always visible input field
- Results dropdown below
- Click to navigate

**Implementation Time:** 15 minutes

**Mockup:**
```
┌────────────────┐
│ [🔍 Search...] │
├────────────────┤
│ Results:       │
│ • Chat 1       │
│ • Message X    │
└────────────────┘
```

---

### **Option 3: Header Global Search** (Like GitHub)

**Location:** Top navigation bar

**Features:**
- Always accessible
- Opens full-screen search on click
- Recent searches

**Implementation Time:** 45 minutes

---

## Keyboard Shortcuts Plan

**Primary:**
- `Cmd/Ctrl + K` → Open search/command palette
- `Cmd/Ctrl + /` → Focus search (alt)
- `Esc` → Close search

**Navigation:**
- `↑` / `↓` → Navigate results
- `Enter` → Open selected result
- `Cmd/Ctrl + Enter` → Open in new tab

**Quick Actions (Future):**
- `Cmd/Ctrl + N` → New chat
- `Cmd/Ctrl + D` → Delete current chat
- `Cmd/Ctrl + E` → Edit chat title
- `Cmd/Ctrl + ,` → Settings

---

## Search Enhancements (Future)

### **1. Search Filters**
```vue
<select v-model="filter">
  <option value="all">All</option>
  <option value="chats">Chats Only</option>
  <option value="messages">Messages Only</option>
  <option value="today">Today</option>
  <option value="week">This Week</option>
</select>
```

### **2. Search Highlighting**
```vue
<span v-html="highlightMatch(text, query)"></span>
```

```typescript
function highlightMatch(text: string, query: string) {
  const regex = new RegExp(`(${query})`, 'gi');
  return text.replace(regex, '<mark>$1</mark>');
}
```

### **3. Recent Searches**
```typescript
const recentSearches = useLocalStorage('recent-searches', []);

function addToRecent(query: string) {
  recentSearches.value = [query, ...recentSearches.value.slice(0, 4)];
}
```

### **4. Search Suggestions**
```typescript
// Show popular searches
const suggestions = [
  'code review',
  'deployment',
  'api documentation'
];
```

---

## Component Architecture

### **Recommended: Command Palette**

```
CommandPalette.vue (Main)
├── SearchInput.vue (Input with keyboard handling)
├── SearchResults.vue (Results list)
│   ├── ResultItem.vue (Single result)
│   └── EmptyState.vue (No results)
└── KeyboardShortcuts.vue (Help overlay)
```

### **State Management:**

```typescript
const isOpen = ref(false);
const query = ref('');
const results = ref<SearchResult[]>([]);
const selectedIndex = ref(0);

// Debounced search
const debouncedSearch = useDebounceFn(async (q: string) => {
  const response = await fetch(`/chat/search?q=${q}`);
  results.value = await response.json();
}, 300);

// Keyboard handler
onKeyStroke('k', (e) => {
  if (e.metaKey || e.ctrlKey) {
    e.preventDefault();
    isOpen.value = true;
  }
});
```

---

## Libraries to Install

```bash
npm install @headlessui/vue@latest  # Accessible UI components
npm install @vueuse/core             # Composables (onKeyStroke, useDebounceFn)
```

**Optional:**
```bash
npm install fuse.js                  # Client-side fuzzy search
npm install mark.js                  # Text highlighting
```

---

## Testing TODO

**Manual Testing:**
1. Open search (Cmd+K)
2. Type "hello"
3. Verify results appear
4. Navigate with arrows
5. Press Enter → Navigate to result
6. Press Esc → Close modal

**E2E Testing (Pest/Dusk):**
```php
test('search returns relevant chats', function () {
    $user = User::factory()->create();
    Chat::factory()->create([
        'user_id' => $user->id,
        'title' => 'Hello World'
    ]);

    $this->actingAs($user)
        ->get('/chat/search?q=hello')
        ->assertJson([
            'results' => [
                ['title' => 'Hello World']
            ]
        ]);
});
```

---

## Performance Considerations

**Current (LIKE search):**
- ✅ Works instantly for <1000 chats
- ⚠️ Slows down at 10,000+ messages
- No indexes on `content` column

**Optimization Path:**
1. **Now:** LIKE search (good enough)
2. **Later (1000+ users):** Add full-text index
3. **Scale (10K+ users):** Migrate to Meilisearch

**Full-text index migration:**
```php
Schema::table('messages', function (Blueprint $table) {
    DB::statement('ALTER TABLE messages ADD FULLTEXT search(content)');
});

// Query:
Message::whereRaw('MATCH(content) AGAINST(? IN BOOLEAN MODE)', [$query]);
```

---

## Deployment Notes

**No changes needed for deployment!**
- Backend API is ready
- Frontend will be pure Vue component
- No database changes
- No environment variables

**Just:**
1. Build frontend assets: `npm run build`
2. Deploy as normal

---

## Priority

**When to build:**
- After testing suite complete
- Before payment integration
- Essential UX feature for 50+ chats

**Estimated value:**
- User delight: ⭐⭐⭐⭐⭐
- Retention impact: High (users keep using)
- Development time: 30-60 min

---

**Status:** Backend ✅ | Frontend ⏳ | Priority: High

**Next:** Implement `CommandPalette.vue` with Cmd+K shortcut
