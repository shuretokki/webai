# Backend Feature Roadmap

## 📅 Created: 2025-12-14

---

## ✅ Completed Features

| Feature                       | Status  | Tests         | Lines of Code | Note Location                              |
| ----------------------------- | ------- | ------------- | ------------- | ------------------------------------------ |
| **File Cleanup System**       | ✅ Done | ✅ 3 tests    | ~120          | `rate-limiting-implementation.md`          |
| **Rate Limiting**             | ✅ Done | ✅ 2 tests    | ~60           | `rate-limiting-implementation.md`          |
| **Usage Tracking**            | ✅ Done | ✅ 8 tests    | ~200          | `usage-tracking-implementation.md`         |
| **Authorization Policies**    | ✅ Done | ✅ 4 tests    | ~150          | `authorization-policies-implementation.md` |
| **Admin Panel (Filament)**    | ✅ Done | N/A (UI)      | ~400          | `filament-admin-panel-implementation.md`   |
| **Search Backend (LIKE)**     | ✅ Done | ✅ 7 tests    | ~50           | `search-frontend-todo.md`                  |
| **Usage Dashboard (Frontend)**| ✅ Done | N/A (UI)      | ~150          | `usage-tracking-implementation.md`         |
| **Testing Suite**             | ✅ Done | ✅ 66 tests   | ~400          | See CHANGELOG.md                           |
| **Real-time Updates**         | ✅ Done | N/A (WebSocket) | ~285        | Below (WebSocket Implementation Notes)     |
| **Frontend Search UI**        | ✅ Done | N/A (UI)      | ~250          | `resources/js/components/chat/SearchModal.vue` |
| **Export Functionality**      | ✅ Done | N/A (Feature) | ~150          | `app/Http/Controllers/ChatController.php` |
| **Multi-Model Support**       | ✅ Done | ✅ 3 tests    | ~400          | `config/ai.php`, `ChatController.php`      |

**Total Backend Code:** ~1,815 lines
**Test Coverage:** 100% pass rate (66/66 tests, 207 assertions)
**Admin Resources:** 3 (User, Chat, UserUsage)
**Frontend Pages:** 2 (Chat, Usage Dashboard)
**Real-time Features:** WebSocket broadcasting with Laravel Reverb
**Production Ready:** All core features ✅

---

## 📡 WebSocket Implementation Notes

**Technology Stack:**
- Backend: Laravel Reverb (WebSocket server)
- Frontend: Laravel Echo Vue (`@laravel/echo-vue` v2.2.6)
- Transport: Pusher protocol with Reverb

**Key Implementation Details:**

1. **Event Naming Convention:**
   - When using `broadcastAs()` in your event class, the frontend must listen with a **leading dot** (`.`)
   - Example: `broadcastAs() { return 'message.sent'; }` → Frontend listens to `.message.sent`
   - The dot prefix tells Laravel Echo to ignore the PHP namespace

2. **Backend Event Structure:**
   ```php
   class MessageSent implements ShouldBroadcast {
       public function broadcastOn(): array {
           return [new PrivateChannel('chats.' . $this->chat->id)];
       }

       public function broadcastAs(): string {
           return 'message.sent'; // Custom event name
       }
   }
   ```

3. **Frontend Listener (Vue Composable):**
   ```typescript
   import { useEcho } from '@laravel/echo-vue';

   useEcho(
       `chats.${chatId}`,     // Channel name
       '.message.sent',       // Event name (note the dot prefix)
       (event) => { ... },    // Callback
       [],                    // Dependencies
       'private'              // Channel type
   );
   ```

4. **Configuration:**
   - Echo is configured globally in `resources/js/app.ts` using `configureEcho()`
   - No need for `window.Echo` - use Vue composables instead
   - Reverb runs on port 8080 (configurable in `.env`)

5. **Private Channels:**
   - Require authentication via `routes/channels.php`
   - Use `Broadcast::channel()` to define authorization logic
   - Example: `Broadcast::channel('chats.{id}', fn($user, $id) => ...)`

**Verified Working:** Real-time message updates in chat interface ✅

---

## 🎯 Next Steps Roadmap

### **Priority 1: Admin Panel Enhancements** 👑 (IN PROGRESS)

**Goal:** Enhance Filament admin panel with revenue dashboard and system monitoring

**Location:** `resources/js/pages/settings/Usage.vue`

**Features:**

- Current month stats display
- Progress bars (messages, tokens, storage)
- Cost breakdown
- Daily usage chart (optional)

**API Endpoints Needed:**

```php
Route::get('/api/usage/current', [UsageController::class, 'current']);
Route::get('/api/usage/history', [UsageController::class, 'history']);
```

**Components to Build:**

1. `UsageDashboard.vue` - Main page
2. `StatCard.vue` - Reusable stat display
3. `ProgressBar.vue` - Quota visualization
4. `UsageChart.vue` - Daily trend (using Chart.js or similar)

**Estimated Time:** 1-2 hours
**Value:** Professional UX, users understand limits

**Mockup:**

```
┌─────────────────────────────────────┐
│     Your Usage This Month           │
├─────────────────────────────────────┤
│  Messages    [████████░░] 45/100   │
│  AI Tokens   12,500 tokens          │
│  Storage     2.3 MB used            │
│  Cost        $1.25                  │
└─────────────────────────────────────┘
```

---

### **Priority 2: Search Functionality** 🔍 (COMPLETED)

**Goal:** Full-text search through chat history

**Implementation Options:**

#### **Option A: Database LIKE (Simple)**

```php
$chats = Chat::where('user_id', auth()->id())
    ->whereHas('messages', function($q) use ($search) {
        $q->where('content', 'like', "%{$search}%");
    })
    ->get();
```

**Pros:** No dependencies, works immediately
**Cons:** Slow with 1000+ messages

#### **Option B: Laravel Scout + Meilisearch (Production)**

```bash
composer require laravel/scout meilisearch/meilisearch-php
```

**Pros:** Fast, typo-tolerant, faceted search
**Cons:** Requires external service

**Features:**

- Search across all chats
- Highlight matching text
- Filter by date/model
- Sort by relevance

**UI Changes:	**

```vue
<input
  v-model="searchQuery"
  placeholder="Search all conversations..."
  @input="debouncedSearch"
/>
```

**Estimated Time:** 45 minutes (Option A) / 2 hours (Option B)
**Value:** Dramatically better UX for power users

---

### **Priority 3: Export Functionality** 📄

**Goal:** Export chat history as PDF/Markdown

**Features:**

1. Export single chat as Markdown
2. Export single chat as PDF
3. Export all chats as ZIP

**Libraries:**

```bash
composer require barryvdh/laravel-dompdf  # PDF generation
```

**Implementation:**

```php
Route::get('/chat/{chat}/export', [ChatController::class, 'export']);

public function export(Chat $chat, string $format = 'md')
{
    $this->authorize('view', $chat);

    return match($format) {
        'pdf' => Pdf::loadView('chat.export', ['chat' => $chat])->download(),
        'md' => response()->streamDownload(function() use ($chat) {
            echo $this->toMarkdown($chat);
        }, "chat-{$chat->id}.md"),
    };
}
```

**UI:**

```vue
<button @click="exportChat('pdf')">
  <i-solar-download-linear /> Export as PDF
</button>
```

**Estimated Time:** 1 hour
**Value:** Users can backup/share conversations

---

### **Priority 5: Real-time Features** ⚡ (WebSockets)

**Goal:** Live updates without page refresh

**Use Cases:**

- See when AI is typing
- Other device sends message → auto-update
- Usage quota updates live

**Stack Options:**

#### **Option A: Laravel Reverb (Recommended - Laravel 11+)**

```bash
php artisan install:broadcasting
composer require laravel/reverb
php artisan reverb:start
```

**Option B: Pusher (Managed Service)**

```bash
composer require pusher/pusher-php-server
```

**Implementation:**

```php
// Backend
event(new MessageSent($chat, $message));

// Frontend
Echo.private(`chats.${chatId}`)
    .listen('MessageSent', (e) => {
        messages.value.push(e.message);
    });
```

**Estimated Time:** 2-3 hours
**Value:** Modern, responsive UX

---

### **Priority 6: Admin Panel** 👑

**Goal:** Monitor users, usage, revenue

**Features:**

- User list with usage stats
- Revenue dashboard
- Quota management (manual overrides)
- System health monitoring

**Tech Stack:**

- **Option A:** Laravel Nova (official, paid)
- **Option B:** Filament PHP (free, modern)
- **Option C:** Custom build

**Recommended:** Filament PHP

```bash
composer require filament/filament
php artisan filament:install --panels
```

**Resources to Build:**

```php
UserResource::class
ChatResource::class
UserUsageResource::class
```

**Estimated Time:** 3-4 hours
**Value:** Essential for production SaaS

---

### **Priority 7: Email Notifications** 📧

**Goal:** Keep users informed

**Scenarios:**

1. Quota warning (80%, 95%)
2. Quota exceeded
3. Weekly usage summary
4. New features announcement

**Implementation:**

```php
// app/Observers/UserUsageObserver.php
if ($stats['messages'] === 80) {
    Mail::to($user)->send(new QuotaWarningMail(80));
}

// app/Console/Commands/SendWeeklySummary.php
Schedule::command('usage:weekly-summary')->weekly();
```

**Templates:**

- `emails/quota-warning.blade.php`
- `emails/quota-exceeded.blade.php`
- `emails/weekly-summary.blade.php`

**Estimated Time:** 2 hours
**Value:** User retention, upgrade prompts

---

### **Priority 3: Polish & Production Ready** ✨

**Goal:** Production-ready improvements

**Goal:** Monetize via subscriptions/credits

**Provider:** Stripe (recommended per our discussion)

**Implementation:**

```bash
composer require laravel/cashier
php artisan cashier:install
```

**Tiers:**

```php
'free' => [
    'messages' => 100,
    'tokens' => 10000,
    'price' => 0,
],
'pro' => [
    'messages' => 1000,
    'tokens' => 100000,
    'price' => 9.99,  // USD or IDR
],
'enterprise' => [
    'messages' => PHP_INT_MAX,
    'tokens' => PHP_INT_MAX,
    'price' => 49.99,
],
```

**Features:**

- Subscription checkout
- Usage-based billing (per token)
- Invoice generation
- Automatic quota updates

**Estimated Time:** 4-6 hours
**Value:** REQUIRED for SaaS revenue

---

### **Priority 4: Email Notifications** 📧 (MOVED TO LAST)

**Goal:** Keep users informed

**Scenarios:**

1. Quota warning (80%, 95%)
2. Quota exceeded
3. Weekly usage summary
4. New features announcement

**Implementation:**

```php
// app/Observers/UserUsageObserver.php
if ($stats['messages'] === 80) {
    Mail::to($user)->send(new QuotaWarningMail(80));
}

// app/Console/Commands/SendWeeklySummary.php
Schedule::command('usage:weekly-summary')->weekly();
```

**Templates:**

- `emails/quota-warning.blade.php`
- `emails/quota-exceeded.blade.php`
- `emails/weekly-summary.blade.php`

**Estimated Time:** 2 hours
**Value:** User retention, upgrade prompts

---

### **Priority 5: API Documentation** 📚

**Goal:** Document endpoints for mobile/third-party apps

**Tools:**

- **Scribe:** Auto-generate docs from code
- **Swagger/OpenAPI:** Industry standard

```bash
composer require knuckleswtf/scribe
php artisan scribe:generate
```

**Output:** `/docs` route with interactive API docs

**Estimated Time:** 1-2 hours
**Value:** Enables ecosystem growth

---

## 🧪 Testing Strategy

### **Current Coverage:**

- Unit Tests: 0
- Feature Tests: 8 (only UsageTracking)
- Integration Tests: 0

**Target Coverage:**

- Unit Tests: 20+ (models, services)
- Feature Tests: 30+ (controllers, routes)
- Integration Tests: 10+ (full user flows)
- **Total Coverage:** 90%+

---

## 📊 Recommended Order (Based on Value)

| Priority | Feature             | Business Value | Technical Debt  | Effort |
| -------- | ------------------- | -------------- | --------------- | ------ |
| 1        | Testing Suite       | ⭐⭐⭐⭐⭐     | Prevents bugs   | Low    |
| 2        | Frontend Dashboard  | ⭐⭐⭐⭐⭐     | Users see value | Med    |
| 3        | Payment Integration | ⭐⭐⭐⭐⭐     | REVENUE         | High   |
| 4        | Email Notifications | ⭐⭐⭐⭐       | Retention       | Low    |
| 5        | Search              | ⭐⭐⭐⭐       | Better UX       | Med    |
| 6        | Admin Panel         | ⭐⭐⭐⭐       | Operations      | High   |
| 7        | Export              | ⭐⭐⭐         | User request    | Low    |
| 8        | Real-time           | ⭐⭐⭐         | Modern UX       | High   |
| 9        | Multi-Model         | ⭐⭐           | Flexibility     | Low    |
| 10       | API Docs            | ⭐⭐           | Future-proof    | Med    |

---

## 🎯 MVP Launch Checklist

Before launching to real users:

### **Backend:**

- [X] File cleanup
- [X] Rate limiting
- [X] Usage tracking
- [ ] Testing (90%+ coverage)
- [ ] Payment integration
- [ ] Email notifications

### **Frontend:**

- [ ] Usage dashboard
- [ ] Upgrade prompts
- [ ] Error handling
- [ ] Loading states
- [ ] Mobile responsive

### **DevOps:**

- [ ] Laravel Forge / Vapor setup
- [ ] Database backups
- [ ] Monitoring (Sentry)
- [ ] Uptime checks
- [ ] SSL certificate

### **Legal:**

- [ ] Privacy policy
- [ ] Terms of service
- [ ] GDPR compliance
- [ ] Cookie consent

---

## 🚀 Current Sprint: Complete Testing

**Now working on:** AttachmentObserverTest.php

**Status:** Backend ✅ | Testing 🔄 | Frontend ⏳

---

## 🚀 Advanced Optimizations (Beyond MVP)

### **Performance Optimizations**

#### **1. Database Query Optimization**

```php
// Current: N+1 query problem
$chats = Chat::all();
foreach ($chats as $chat) {
    echo $chat->messages->count();  // Query per chat!
}

// Optimized: Single query
$chats = Chat::withCount('messages')->get();
foreach ($chats as $chat) {
    echo $chat->messages_count;  // No extra queries!
}
```

#### **2. Redis Caching for Usage Stats**

```bash
composer require predis/predis
```

```php
// Cache hot data
public function currentMonthUsage(): array
{
    return Cache::remember(
        "user:{$this->id}:usage:" . now()->format('Y-m'),
        3600, // 1 hour
        fn() => $this->usages()
            ->whereMonth('created_at', now())
            ->selectRaw('...')
            ->first()
    );
}

// Invalidate on new usage
UserUsage::created(function($usage) {
    Cache::forget("user:{$usage->user_id}:usage:" . now()->format('Y-m'));
});
```

**Impact:** 95% faster dashboard loads

---

#### **3. Lazy Loading Prevention**

```php
// Add to AppServiceProvider
Model::preventLazyLoading(! app()->isProduction());
```

Throws exception in dev if you forget `->with()`, forces good habits.

---

#### **4. Database Indexing**

```php
// Add to existing migrations
Schema::table('user_usages', function (Blueprint $table) {
    $table->index(['user_id', 'created_at', 'type']);  // Composite index
});

Schema::table('messages', function (Blueprint $table) {
    $table->fullText('content');  // For search
});
```

**Impact:** 10x faster queries on large datasets

---

#### **5. Chunk Large Deletes**

```php
// Instead of: Chat::where('user_id', $id)->delete();
Chat::where('user_id', $id)->chunkById(100, function($chats) {
    $chats->each->forceDelete();
});
```

Prevents memory exhaustion on large operations.

---

### **Security Enhancements**

#### **6. Authorization Policies**

```php
// app/Policies/ChatPolicy.php
class ChatPolicy
{
    public function view(User $user, Chat $chat): bool
    {
        return $user->id === $chat->user_id;
    }

    public function delete(User $user, Chat $chat): bool
    {
        return $user->id === $chat->user_id && !$chat->trashed();
    }
}

// ChatController
public function destroy(Chat $chat)
{
    $this->authorize('delete', $chat);
    $chat->delete();
}
```

---

#### **7. Rate Limiting by IP + User**

```php
RateLimiter::for('chat-messages', function (Request $request) {
    $key = $request->user()
        ? "user:{$request->user()->id}"
        : "ip:{$request->ip()}";

    return Limit::perMinute(10)->by($key);
});
```

Prevents abuse from both logged-in and guest users.

---

#### **8. Input Sanitization**

```bash
composer require htmlpurifier/htmlpurifier
```

```php
// Prevent XSS in chat messages
$request->validate([
    'prompt' => ['required', 'string', new NoHtmlTags],
]);
```

---

#### **9. Content Moderation**

```php
// Integrate OpenAI Moderation API
$response = Http::post('https://api.openai.com/v1/moderations', [
    'input' => $request->input('prompt'),
])->json();

if ($response['results'][0]['flagged']) {
    return response()->json(['error' => 'Inappropriate content'], 422);
}
```

---

### **Monitoring & Observability**

#### **10. Error Tracking (Sentry)**

```bash
composer require sentry/sentry-laravel
```

```php
// Auto-track errors
Sentry::captureException($exception);
```

**Dashboard shows:**

- Error trends
- User-affected count
- Stack traces

---

#### **11. Performance Monitoring**

```bash
composer require spatie/laravel-ray
```

```php
// Debug queries in development
ray(DB::getQueryLog());
ray($user->currentMonthUsage())->red();
```

---

#### **12. Health Checks**

```php
// routes/web.php
Route::get('/health', function() {
    return [
        'status' => 'ok',
        'database' => DB::connection()->getPdo() ? 'ok' : 'fail',
        'redis' => Cache::store('redis')->get('health') ? 'ok' : 'fail',
        'queue' => Queue::size() < 1000 ? 'ok' : 'warn',
    ];
});
```

---

### **Scalability Improvements**

#### **13. Horizontal Scaling with Load Balancer**

```
     [Load Balancer]
         /    \
    [App 1]  [App 2]
         \    /
       [Database]
       [Redis]
       [S3]
```

**Requirements:**

- Stateless sessions (database/Redis)
- Shared file storage (S3, not local)
- Centralized queue (Redis/SQS)

---

#### **14. Read Replicas**

```php
// config/database.php
'mysql' => [
    'read' => [
        'host' => ['replica1.mysql', 'replica2.mysql'],
    ],
    'write' => [
        'host' => ['master.mysql'],
    ],
],
```

**Impact:** 3x read capacity

---

#### **15. CDN for Static Assets**

```php
// Use Cloudflare/CloudFront
'asset_url' => env('ASSET_URL', 'https://cdn.yourapp.com'),
```

---

## 🆕 Additional Feature Ideas

### **11. Chat Sharing**

```php
Route::get('/chat/{chat}/share', [ChatController::class, 'share']);

// Generate shareable link
public function share(Chat $chat)
{
    $this->authorize('view', $chat);

    $token = Str::random(32);

    ShareableChat::create([
        'chat_id' => $chat->id,
        'token' => $token,
        'expires_at' => now()->addDays(7),
    ]);

    return url("/shared/{$token}");
}
```

**UI:** Copy button → `https://app.com/shared/abc123`

---

### **12. AI Model Comparison**

```vue
<button @click="compareModels(['gemini-2.0-flash', 'gpt-4'])">
  Compare Responses
</button>
```

Show side-by-side responses from different models.

---

### **13. Voice Input (Speech-to-Text)**

```javascript
// Frontend
const recognition = new webkitSpeechRecognition();
recognition.onresult = (e) => {
    prompt.value = e.results[0][0].transcript;
};
recognition.start();
```

---

### **14. Chat Templates**

```php
// Pre-defined prompts
ChatTemplate::create([
    'name' => 'Code Review',
    'prompt' => 'Review this code for bugs and improvements:',
    'model' => 'gemini-1.5-pro',
]);
```

---

### **15. Feedback System**

```vue
<button @click="rate(message, 'good')">👍</button>
<button @click="rate(message, 'bad')">👎</button>
```

Track which responses users find helpful.

---

### **16. Dark Mode / Themes**

```vue
<button @click="toggleTheme()">
  <i-solar-sun v-if="!dark" />
  <i-solar-moon v-else />
</button>
```

---

### **17. Keyboard Shortcuts**

```javascript
// Cmd+K = New Chat
// Cmd+/ = Search
// Cmd+Enter = Send Message

document.addEventListener('keydown', (e) => {
    if (e.metaKey && e.key === 'k') {
        createNewChat();
    }
});
```

---

### **18. Chat Folders**

```php
$user->chatFolders()->create(['name' => 'Work']);
$chat->update(['folder_id' => $folderId]);
```

Organize chats like email folders.

---

### **19. AI Personas**

```php
// Change AI personality
'system_prompt' => match($persona) {
    'professional' => 'You are a formal business assistant.',
    'casual' => 'You are a friendly, casual helper.',
    'technical' => 'You are an expert software engineer.',
},
```

---

### **20. Collaborative Chats**

```php
// Share chat with team members
$chat->collaborators()->attach($userId);
```

Multiple users in same chat.

---

## 📈 Production Deployment Checklist

### **Infrastructure**

- [ ] **Hosting**: Laravel Forge / AWS / DigitalOcean
- [ ] **SSL Certificate**: Let's Encrypt (free)
- [ ] **Database**: MySQL 8.0+ with backups
- [ ] **Redis**: For cache + queues
- [ ] **Queue Worker**: Supervisor running `queue:work`
- [ ] **Scheduler**: Cron running `schedule:run`
- [ ] **File Storage**: S3 or equivalent
- [ ] **CDN**: Cloudflare for static assets

---

### **Security**

- [ ] **Environment**: `.env` not in git
- [ ] **Keys**: Rotate `APP_KEY` after first deploy
- [ ] **Database**: Strong passwords, no root access
- [ ] **HTTPS**: Force SSL in production
- [ ] **CORS**: Configure allowed origins
- [ ] **CSP Headers**: Prevent XSS
- [ ] **SQL Injection**: Use prepared statements (Laravel does this)

---

### **Performance**

- [ ] **Opcache**: Enable PHP opcache
- [ ] **Gzip**: Enable compression
- [ ] **HTTP/2**: Enable on Nginx/Apache
- [ ] **Asset Minification**: `npm run build`
- [ ] **Image Optimization**: Compress uploads
- [ ] **Database Indexing**: All foreign keys + common queries

---

### **Monitoring**

- [ ] **Uptime**: UptimeRobot (free)
- [ ] **Errors**: Sentry or Flare
- [ ] **Logs**: Logtail or Papertrail
- [ ] **Metrics**: Laravel Pulse
- [ ] **Analytics**: Google Analytics or Plausible

---

### **Legal**

- [ ] **Privacy Policy**: Required for GDPR
- [ ] **Terms of Service**: Protect your business
- [ ] **Cookie Consent**: EU requirement
- [ ] **Data Processing Agreement**: For enterprise clients

---

## 🏁 Launch Strategy

### **Phase 1: Beta (Invite-Only)**

- 50-100 users
- Free tier only
- Gather feedback
- Fix critical bugs

### **Phase 2: Public Launch**

- Open registration
- Introduce Pro tier ($9.99/mo)
- Marketing push (ProductHunt, HackerNews)

### **Phase 3: Growth**

- Referral program (10% discount)
- Enterprise tier ($49.99/mo)
- API access ($99/mo)

---

**Status:** Backend ✅ | Testing 🔄 | Frontend ⏳ | Deployment ⏳
