# Usage Tracking Implementation - Completed ✅

## 📅 Completed: 2025-12-13

---

## What We Implemented

### **Purpose: Foundation for SaaS Monetization**

Track user consumption to enable:
- Quota enforcement (free tier: 100 messages/month)
- Accurate billing (per token/message/storage)
- Usage analytics and dashboards
- Abuse prevention

---

## Database Schema

### **Migration: `create_user_usages_table`**

**Table: `user_usages`**

| Column | Type | Purpose |
|--------|------|---------|
| `id` | bigint | Primary key |
| `user_id` | bigint | Foreign key to users (cascades on delete) |
| `type` | string | Usage category (`message_sent`, `ai_response`, `file_upload`) |
| `tokens` | integer | AI tokens consumed (for billing) |
| `messages` | integer | Message count |
| `bytes` | bigint | Storage bytes used |
| `cost` | decimal(10,4) | Calculated USD cost |
| `metadata` | json | Context (chat_id, model, filename, etc.) |
| `created_at` | timestamp | When usage occurred |

**Indexes:**
- `(user_id, type, created_at)` - Fast filtering by type
- `(user_id, created_at)` - Fast monthly aggregations

**Files Modified:**
- `database/migrations/2025_12_13_100150_create_user_usages_table.php`
- `app/Models/UserUsage.php`
- `app/Models/User.php`
- `app/Http/Controllers/ChatController.php`

---

## Key Features

### **1. UserUsage Model**

**Static `record()` Method:**
```php
UserUsage::record(
    userId: $user->id,
    type: 'ai_response',
    tokens: 1500,
    messages: 1,
    bytes: 0,
    metadata: ['chat_id' => 123, 'model' => 'gemini-2.0-flash-lite']
);
```

**Auto-Cost Calculation:**
```php
protected static function calculateCost(
    string $type,
    int $tokens,
    int $messages,
    int $bytes
): float {
    return match($type) {
        'ai_response' => $tokens * 0.0001,  // $0.10 per 1000 tokens
        'file_upload' => $bytes * 0.00000001, // $0.01 per GB
        default => 0,
    };
}
```

**Explanation:**
- **Named arguments**: More readable than positional
- **Match expression**: Clean type-based pricing
- **Extensible**: Easy to add new usage types

---

### **2. User Model Methods**

**Get Current Month Usage:**
```php
$stats = $user->currentMonthUsage();
// Returns: ['messages' => 45, 'tokens' => 12500, 'cost' => 1.25]
```

**SQL Behind the Scenes:**
```sql
SELECT
    SUM(messages) as total_messages,
    SUM(tokens) as total_tokens,
    SUM(bytes) as total_bytes,
    SUM(cost) as total_cost
FROM user_usages
WHERE user_id = ?
  AND MONTH(created_at) = MONTH(NOW())
  AND YEAR(created_at) = YEAR(NOW())
```

**Check Quota:**
```php
if ($user->hasExceededQuota('messages', 100)) {
    return response()->json(['error' => 'Quota exceeded'], 403);
}
```

**Match-based Flexibility:**
```php
return match($type) {
    'messages' => $usage['messages'] >= $limit,
    'tokens' => $usage['tokens'] >= $limit,
    'bytes' => $usage['bytes'] >= $limit,
    default => false,
};
```

---

### **3. ChatController Integration**

**Three Tracking Points:**

**A. File Upload (Lines 100-109)**
```php
UserUsage::record(
    userId: $user->id,
    type: 'file_upload',
    bytes: $file->getSize(),
    metadata: [
        'chat_id' => $chat->id,
        'mime_type' => $file->getMimeType(),
        'filename' => $file->getClientOriginalName(),
    ]
);
```

**B. Message Sent (Lines 125-134)**
```php
UserUsage::record(
    userId: $user->id,
    type: 'message_sent',
    messages: 1,
    metadata: [
        'chat_id' => $chat->id,
        'model' => $model,
        'has_attachments' => !empty($attachmentsData),
    ]
);
```

**C. AI Response (Lines 175-184)**
```php
// Token estimation: ~4 chars per token
$totalTokens += (int) (strlen($text) / 4);

UserUsage::record(
    userId: $user->id,
    type: 'ai_response',
    tokens: $totalTokens,
    metadata: [
        'chat_id' => $chat->id,
        'model' => $model,
        'response_length' => strlen($fullResponse),
    ]
);
```

**Quota Check (Lines 55-59):**
```php
if ($user->hasExceededQuota('messages', 100)) {
    return response()->json([
        'error' => 'You have reached your monthly message limit. Upgrade your plan to increase limit.',
    ], 403);
}
```

**Explanation:**
- Checked BEFORE processing (saves API costs)
- HTTP 403 (Forbidden) is semantically correct
- Clear upgrade prompt

---

## TODO: Frontend Implementation

### **Priority: HIGH**
### **Estimated Time: 2-3 hours**

### **Features Needed:**

**1. Usage Dashboard (New Page)**

**Location:** Create `resources/js/pages/settings/Usage.vue`

**Display:**
- Current month stats (messages, tokens, storage)
- Progress bars (45/100 messages used)
- Cost breakdown
- Daily/weekly usage charts

**Example Component:**
```vue
<script setup lang="ts">
const { data: usage } = await useFetch('/api/usage/current');
</script>

<template>
  <div class="usage-dashboard">
    <h1>Your Usage</h1>

    <div class="stat-card">
      <h3>Messages</h3>
      <progress :value="usage.messages" max="100"></progress>
      <p>{{ usage.messages }} / 100 this month</p>
    </div>

    <div class="stat-card">
      <h3>AI Tokens</h3>
      <p>{{ usage.tokens.toLocaleString() }} tokens</p>
      <p class="cost">${{ usage.cost.toFixed(2) }}</p>
    </div>

    <div class="stat-card">
      <h3>Storage</h3>
      <p>{{ formatBytes(usage.bytes) }}</p>
    </div>
  </div>
</template>
```

**2. API Endpoint**

**Location:** `app/Http/Controllers/UsageController.php`

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UsageController extends Controller
{
    public function current(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'current_month' => $user->currentMonthUsage(),
            'limit' => 100, // From tier
            'percentage' => ($user->currentMonthUsage()['messages'] / 100) * 100,
        ]);
    }

    public function history(Request $request)
    {
        return $request->user()
            ->usages()
            ->latest()
            ->paginate(50);
    }
}
```

**Route:**
```php
Route::get('/api/usage/current', [UsageController::class, 'current']);
Route::get('/api/usage/history', [UsageController::class, 'history']);
```

**3. Quota Warning UI**

**Location:** `resources/js/components/chat/ChatInput.vue`

```vue
<div v-if="quotaWarning" class="quota-warning bg-yellow-100 p-3 rounded mb-2">
  ⚠️ You've used {{ usage.messages }}/100 free messages this month.
  <a href="/upgrade" class="text-blue-600">Upgrade now</a>
</div>

<script setup>
const quotaWarning = computed(() => usage.messages >= 90);
</script>
```

**4. Error Handling for 403**

**Location:** `resources/js/pages/chat/Index.vue`

```typescript
if (response.status === 403) {
    const error = await response.json();

    // Show modal or toast
    alert(error.error);

    // Redirect to upgrade page
    router.visit('/upgrade');

    return;
}
```

---

## Optimization Recommendations

### **Short-term (Next Session):**

**1. More Accurate Token Counting**
```php
// Instead of: strlen($text) / 4
// Use Gemini's token counting API:

use Google\Cloud\AIPlatform\V1\CountTokensRequest;

$tokenCount = $aiClient->countTokens(new CountTokensRequest([
    'contents' => [$fullResponse],
]));

UserUsage::record(
    tokens: $tokenCount->getTotalTokens()
);
```

**2. Async Usage Recording (Performance)**
```php
// Instead of: UserUsage::record(...) (blocks request)
// Use: Queue jobs for non-critical tracking

dispatch(new RecordUsageJob($user->id, 'ai_response', $tokens));
```

**3. Caching Current Month Stats**
```php
// In User model
public function currentMonthUsage(): array
{
    return Cache::remember(
        "user:{$this->id}:usage:" . now()->format('Y-m'),
        3600, // 1 hour cache
        fn() => $this->usages()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->selectRaw('...')
            ->first()
    );
}
```

### **Medium-term (Production Ready):**

**4. Tiered Quotas (Free/Pro/Enterprise)**
```php
// app/Models/User.php

public function getQuota(string $type): int
{
    return match($this->subscription_tier) {
        'free' => match($type) {
            'messages' => 100,
            'tokens' => 10000,
            'bytes' => 100 * 1024 * 1024, // 100MB
        },
        'pro' => match($type) {
            'messages' => 1000,
            'tokens' => 100000,
            'bytes' => 10 * 1024 * 1024 * 1024, // 10GB
        },
        'enterprise' => PHP_INT_MAX, // Unlimited
        default => 0,
    };
}

public function hasExceededQuota(string $type): bool
{
    $usage = $this->currentMonthUsage();
    $limit = $this->getQuota($type);

    return match($type) {
        'messages' => $usage['messages'] >= $limit,
        'tokens' => $usage['tokens'] >= $limit,
        'bytes' => $usage['bytes'] >= $limit,
        default => false,
    };
}
```

**5. Usage Alerts (Email Notifications)**
```php
// app/Observers/UserUsageObserver.php

public function created(UserUsage $usage)
{
    $user = $usage->user;
    $stats = $user->currentMonthUsage();
    $limit = $user->getQuota('messages');

    // 80% quota warning
    if ($stats['messages'] === (int)($limit * 0.8)) {
        Mail::to($user)->send(new QuotaWarningMail(80));
    }

    // 95% quota warning
    if ($stats['messages'] === (int)($limit * 0.95)) {
        Mail::to($user)->send(new QuotaWarningMail(95));
    }
}
```

**6. Analytics Dashboard**
```php
// Track trends over time

Route::get('/admin/analytics', function() {
    $dailyUsage = UserUsage::query()
        ->selectRaw('DATE(created_at) as date, type, SUM(messages) as total, SUM(cost) as revenue')
        ->groupBy('date', 'type')
        ->orderBy('date', 'desc')
        ->take(30)
        ->get();

    return Inertia::render('Admin/Analytics', [
        'daily_usage' => $dailyUsage,
    ]);
});
```

### **Long-term (Scale):**

**7. Data Retention Policy**
```php
// Cleanup old usage data (GDPR compliance)

Schedule::command('usage:cleanup')->monthly();

// app/Console/Commands/CleanupOldUsage.php
UserUsage::where('created_at', '<', now()->subMonths(24))->delete();
```

**8. Real-time Usage Updates (WebSockets)**
```php
// Broadcast usage updates to frontend

event(new UsageUpdatedEvent($user->id, $user->currentMonthUsage()));

// Frontend listens:
Echo.private(`users.${userId}`)
    .listen('UsageUpdatedEvent', (e) => {
        usage.value = e.stats;
    });
```

---

## Testing Commands

```bash
# Run migration
php artisan migrate

# Test in Tinker
php artisan tinker
```

```php
// Check recent usage
\App\Models\UserUsage::latest()->take(10)->get();

// Your current stats
$user = \App\Models\User::find(1);
$user->currentMonthUsage();

// Test quota check
$user->hasExceededQuota('messages', 2);
// Send 3 messages, 3rd should be blocked!

// Test cost calculation
\App\Models\UserUsage::record(
    userId: 1,
    type: 'ai_response',
    tokens: 10000
);
// Cost should be: 10000 * 0.0001 = $1.00

// View all usage types
\App\Models\UserUsage::distinct('type')->pluck('type');
// ['message_sent', 'ai_response', 'file_upload']

// Monthly spending
$user->usages()
    ->whereMonth('created_at', now()->month)
    ->sum('cost');
```

---

## Current Pricing Configuration

| Usage Type | Formula | Example | Cost |
|------------|---------|---------|------|
| AI Response | `tokens × 0.0001` | 10,000 tokens | $1.00 |
| File Upload | `bytes × 0.00000001` | 1GB (1,073,741,824 bytes) | $0.11 |
| Message Sent | `0` (free) | 100 messages | $0.00 |

**Note:** Pricing is simplified for demo. In production, use actual Gemini API pricing:
- Flash models: ~$0.075 per 1M input tokens
- Pro models: ~$1.25 per 1M input tokens

---

## What We Learned

1. **Database Design** - Flexible schema for multiple usage types
2. **Aggregation Queries** - `SUM()`, `WHERE MONTH()` for billing cycles
3. **Match Expressions** - Clean type-based logic (PHP 8.0+)
4. **Named Arguments** - Readable function calls (PHP 8.0+)
5. **Quota Enforcement** - Pre-emptive checks to protect costs
6. **JSON Metadata** - Flexible context storage for analytics
7. **Production Patterns** - Caching, queueing, observers

---

## Integration Points

### **Where Usage is Tracked:**

1. **ChatController::stream()**
   - Line 55: Quota check (before processing)
   - Line 100: File upload
   - Line 125: Message sent
   - Line 175: AI response with tokens

### **Where Quotas Are Enforced:**

- ChatController::stream() - Blocks at 100 messages/month

### **Where Costs Are Calculated:**

- UserUsage::calculateCost() - Auto-calculated on record

---

## Next Steps

After implementing frontend, consider:

- [ ] **Payment Integration** (Stripe for usage-based billing)
- [ ] **Email Notifications** (quota warnings)
- [ ] **Admin Analytics** (usage trends, revenue tracking)
- [ ] **Export Usage Data** (CSV/PDF for accounting)
- [ ] **API Rate Limiting** (tie to usage quotas)

---

## Database Queries for Reporting

```php
// Top users by spending
User::withSum('usages as total_cost', 'cost')
    ->orderByDesc('total_cost')
    ->take(10)
    ->get();

// Revenue this month
UserUsage::whereMonth('created_at', now()->month)
    ->sum('cost');

// Most active chats
UserUsage::query()
    ->select('metadata->chat_id as chat_id')
    ->selectRaw('COUNT(*) as interactions')
    ->groupBy('chat_id')
    ->orderByDesc('interactions')
    ->take(10)
    ->get();

// Model usage distribution
UserUsage::where('type', 'ai_response')
    ->select('metadata->model as model')
    ->selectRaw('COUNT(*) as uses, SUM(tokens) as total_tokens')
    ->groupBy('model')
    ->get();
```

---

**Created:** 2025-12-13
**Last Updated:** 2025-12-13
**Status:** Backend ✅ | Frontend ⏳ Pending
**Lines of Code:** ~200
**Files Modified:** 4
