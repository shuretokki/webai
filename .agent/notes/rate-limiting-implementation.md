# Rate Limiting Implementation - Completed ✅

## 📅 Completed: 2025-12-13

---

## What We Implemented

### 1. **File Cleanup System**

- **Observer Pattern**: `AttachmentObserver` deletes files from disk when attachments are force-deleted
- **Soft Deletes**: Records stay in DB for 30 days (recovery window)
- **Auto-Cleanup**: Scheduled command runs daily to permanently delete old files
- **Files Modified**:
  - `app/Observers/AttachmentObserver.php`
  - `app/Models/Attachment.php` (added `SoftDeletes` trait)
  - `app/Console/Commands/CleanupOldAttachments.php`
  - `routes/console.php` (scheduled daily)
  - Migration: `add_soft_deletes_to_attachments_table.php`

**Key Learning:**

- Observers decouple business logic from controllers
- `forceDeleting` vs `deleting` event timing
- Storage facade for safe file operations
- Scheduled tasks for maintenance

---

### 2. **Rate Limiting**

- **Chat Messages**: 2 per minute per user (configurable)
- **General API**: 60 per minute per IP
- **Custom Error Response**: 429 status with JSON message
- **Files Modified**:
  - `app/Providers/AppServiceProvider.php` (added `configureRateLimiting()`)
  - `routes/web.php` (added `throttle:chat-messages` middleware)

**Key Learning:**

- Laravel's `RateLimiter` facade
- `Limit::perMinute()` chaining
- Middleware application
- CSRF vs Rate Limit (419 vs 429)

---

## TODO: Frontend Handling for Rate Limiting

### **Priority: MEDIUM**

### **Estimated Time: 30 minutes**

### Current Behavior:

- Backend blocks requests with 429 status
- Frontend receives error but doesn't show user-friendly message
- User sees silent failure or generic error

### Required Implementation:

**Location:** `resources/js/pages/chat/Index.vue`

**In `handleSendMessage` function, add error handling:**

```typescript
try {
    const response = await fetch('/chat/stream', {
        // ... existing code
    });

    // Add this check AFTER fetch:
    if (response.status === 429) {
        const errorData = await response.json();

        // Show toast notification or inline message
        alert(errorData.error); // Temp solution
        // TODO: Replace with proper toast/notification component

        return; // Stop processing
    }

    if (!response.body) throw new Error('No response body');

    // ... rest of stream logic
} catch (error) {
    console.error('Stream failed', error);

    // TODO: Show user-friendly error message
}
```

### Better UX Implementation:

**1. Disable Send Button on Rate Limit:**

```typescript
const isRateLimited = ref(false);
const rateLimitTimer = ref<number | null>(null);

// In error handler:
if (response.status === 429) {
    isRateLimited.value = true;

    // Re-enable after 60 seconds
    rateLimitTimer.value = setTimeout(() => {
        isRateLimited.value = false;
    }, 60000);
}

// In template:
<ChatInput
    @submit="handleSendMessage"
    :disabled="isRateLimited || isStreaming"
/>
```

**2. Add Toast Notification:**

```bash
# Install a toast library or use built-in if available
npm install vue-toastification
```

**3. Show Countdown Timer:**

```typescript
const rateLimitCountdown = ref(0);

if (response.status === 429) {
    rateLimitCountdown.value = 60;

    const interval = setInterval(() => {
        rateLimitCountdown.value--;
        if (rateLimitCountdown.value <= 0) {
            clearInterval(interval);
            isRateLimited.value = false;
        }
    }, 1000);
}

// Template:
<div v-if="isRateLimited" class="text-yellow-500">
    Too many messages. Try again in {{ rateLimitCountdown }}s
</div>
```

---

## Optimization Recommendations

### **Short-term (Next Session):**

1. **Increase Rate Limit After Testing**

   ```php
   // In AppServiceProvider.php
   return Limit::perMinute(10)  // Change from 2 to 10
       ->by($request->user()->id);
   ```
2. **Add Rate Limit Headers**

   ```php
   // Shows remaining requests in response headers
   return Limit::perMinute(10)
       ->by($request->user()->id)
       ->response(function (Request $request, array $headers) {
           return response()->json([
               'error' => 'Too many messages.',
               'retry_after' => $headers['Retry-After'] ?? 60
           ], 429);
       });
   ```
3. **Different Limits for Premium Users**

   ```php
   RateLimiter::for('chat-messages', function (Request $request) {
       $limit = $request->user()->isPremium() ? 50 : 10;

       return Limit::perMinute($limit)
           ->by($request->user()->id);
   });
   ```

### **Medium-term (Future Features):**

4. **Redis for Distributed Rate Limiting**

   - Current: Uses file cache (single server)
   - Upgrade: Redis for multi-server deployments

   ```bash
   composer require predis/predis
   # Update .env: CACHE_DRIVER=redis
   ```
5. **Rate Limit by Tokens (for AI cost control)**

   ```php
   // Track AI tokens instead of requests
   RateLimiter::for('ai-tokens', function (Request $request) {
       return Limit::perMinute(10000)  // 10k tokens/min
           ->by($request->user()->id);
   });

   // In controller, decrement based on actual usage
   RateLimiter::hit('ai-tokens:' . $user->id, $tokensUsed);
   ```
6. **Implement Sliding Window (More Accurate)			**			

   ```php
   // Current: Fixed window (resets every minute)
   // Better: Sliding window (last 60 seconds)

   return Limit::perMinute(10)
       ->by($request->user()->id)
       ->sliding(60);  // Laravel 11+
   ```

### **Long-term (Production):**

7. **Monitoring & Alerts**

   ```php
   // Log rate limit hits for analysis
   RateLimiter::for('chat-messages', function (Request $request) {
       return Limit::perMinute(10)
           ->by($request->user()->id)
           ->response(function () use ($request) {
               \Log::warning('Rate limit hit', [
                   'user_id' => $request->user()->id,
                   'ip' => $request->ip(),
               ]);

               return response()->json(['error' => '...'], 429);
           });
   });
   ```
8. **Dynamic Rate Limits Based on Server Load**

   ```php
   $serverLoad = sys_getloadavg()[0];
   $limit = $serverLoad > 5 ? 5 : 10;  // Reduce during high load

   return Limit::perMinute($limit)->by(...);
   ```
9. **Whitelist IPs (Admin/Bot)**

   ```php
   RateLimiter::for('chat-messages', function (Request $request) {
       if (in_array($request->ip(), ['127.0.0.1', 'office-ip'])) {
           return Limit::none();  // No rate limit
       }

       return Limit::perMinute(10)->by(...);
   });
   ```

---

## Current Configuration

| Limiter           | Limit  | Window | Tracked By | Status    |
| ----------------- | ------ | ------ | ---------- | --------- |
| `chat-messages` | 2/min  | 60s    | User ID    | ✅ Active |
| `api`           | 60/min | 60s    | IP Address | ✅ Active |

---

## Testing Commands

```bash
# Test cleanup manually
php artisan attachments:cleanup

# Test scheduler (runs all scheduled tasks)
php artisan schedule:run

# Keep scheduler running (dev mode)
php artisan schedule:work

# Test rate limit from CLI
php artisan tinker
> RateLimiter::availableIn('chat-messages:1');  // seconds until reset
```

---

## What We Learned

1. **Observers** - Decouple side effects from main logic
2. **Soft Deletes** - Recovery safety net (30-day window)
3. **Scheduled Tasks** - Automated maintenance (cron alternative)
4. **Rate Limiting** - Protect costs and server resources
5. **Middleware** - Cross-cutting concerns (auth, throttle, etc.)
6. **Production Patterns** - File cleanup, error handling, security

---

## Next Steps

After implementing frontend handling, consider:

- [ ] Usage tracking (foundation for SaaS billing)
- [ ] Testing (PHPUnit/Pest for reliability)
- [ ] Search functionality (full-text search in chats)
- [ ] Export chats (PDF/Markdown generation)
- [ ] Real-time features (WebSockets/Pusher)

---

**Created:** 2025-12-13
**Last Updated:** 2025-12-13
**Status:** Backend ✅2				 | Frontend ⏳ Pending
