# Authorization Policies Implementation - Completed ✅

## 📅 Completed: 2025-12-14

---

## What We Implemented

### **Purpose: Centralized Security & Authorization**

Implement Laravel Policies with Route Model Binding to ensure users can only access, modify, and delete their own resources. This replaces manual authorization checks with automatic, declarative security enforced at the route level.

---

## Architecture Overview

### **3-Layer Security System**

```
Routes (Gatekeeper)
   ↓ (middleware: can:delete,chat)
Policy (Brain)
   ↓ (ChatPolicy::delete())
Controller (Worker)
   ↓ (executes if authorized)
```

**Flow:**
1. **Route Middleware** - Intercepts request, extracts model from URL
2. **Policy Check** - Runs authorization logic, returns true/false
3. **Controller** - Only executes if policy approved, otherwise 403

---

## Key Components

### **1. ChatPolicy**

**File:** `app/Policies/ChatPolicy.php`

**Methods Implemented:**

```php
public function view(User $user, Chat $chat): bool
{
    return $user->id === $chat->user_id;
}

public function update(User $user, Chat $chat): bool
{
    return $user->id === $chat->user_id;
}

public function delete(User $user, Chat $chat): bool
{
    return $user->id === $chat->user_id && !$chat->trashed();
}

public function restore(User $user, Chat $chat): bool
{
    return $user->id === $chat->user_id && $chat->trashed();
}

public function forceDelete(User $user, Chat $chat): bool
{
    return $user->id === $chat->user_id;
}
```

**Explanation:**
- Each method returns `bool` (authorized or not)
- Takes `User` (current user) and `Chat` (resource being accessed)
- Single responsibility per method
- Self-documenting with clear names

---

### **2. Route Middleware Authorization**

**File:** `routes/web.php`

**Before (Manual in Controller):**
```php
Route::delete('/chat/{chat}', [ChatController::class, 'destroy']);

/* Inside controller: */
public function destroy(Chat $chat)
{
    if (auth()->user()->id !== $chat->user_id) {  // ❌ Manual check
        abort(403);
    }
    $chat->delete();
}
```

**After (Automatic at Route Level):**
```php
Route::delete('/chat/{chat}', [ChatController::class, 'destroy'])
    ->can('delete', 'chat');  // ✅ Automatic

/* Inside controller: */
public function destroy(Chat $chat)
{
    $chat->delete();  // Policy already checked!
}
```

**What `.can('delete', 'chat')` does:**
1. Extracts `{chat}` from URL
2. Calls `ChatPolicy::delete($user, $chat)`
3. If `false` → Returns 403 Forbidden
4. If `true` → Continues to controller

---

### **3. Route Model Binding**

**Migration from Query Parameters to Route Parameters**

**Before:**
```
URL: /chat?chat_id=123
Route: Route::get('/chat', ...)
Controller:
  $chatId = $request->query('chat_id');
  $chat = Chat::find($chatId);
  if (!$chat) abort(404);
```

**After:**
```
URL: /chat/123
Route: Route::get('/chat/{chat?}', ...)
Controller:
  public function index(Request $request, ?Chat $chat = null)
  {
      // $chat is auto-loaded by Laravel!
  }
```

**Benefits:**
- ✅ **Automatic model loading** from URL
- ✅ **Auto 404** if not found
- ✅ **RESTful URLs** (/chat/123 vs ?chat_id=123)
- ✅ **Type hints** provide IDE support
- ✅ **Less boilerplate** code

---

## Route Parameters Deep Dive

### **Query Parameters vs Route Parameters**

| Aspect | Query (?chat_id=123) | Route (/chat/123) |
|--------|---------------------|-------------------|
| **Semantic** | ❌ Not part of path | ✅ Part of resource identity |
| **RESTful** | ❌ Not standard | ✅ Standard REST |
| **SEO** | ❌ Less friendly | ✅ Clean URLs |
| **Caching** | ❌ Harder to cache | ✅ CDN-friendly |
| **Model Binding** | ❌ Manual | ✅ Automatic |
| **Type Safety** | ❌ Strings only | ✅ Typed models |
| **Best For** | Filters, pagination | Resource IDs |

**When to use Query Parameters:**
- Filters: `?status=active&sort=date`
- Pagination: `?page=2`
- Search: `?q=hello`
- Optional modifiers: `?debug=true`

**When to use Route Parameters:**
- Resource IDs: `/user/456`
- Hierarchical resources: `/blog/post/789/comments`
- Actions on resources: `/chat/123/export`

---

## Frontend Integration (Wayfinder)

### **Migration Path**

**Before:**
```vue
<Link :href="Chat({ query: { chat_id: chat.id } }).url">
  <!-- Generates: /chat?chat_id=123 -->
```

**After:**
```vue
<Link :href="Chat({ chat: chat.id }).url">
  <!-- Generates: /chat/123 -->
```

**Regenerate Routes:**
```bash
php artisan wayfinder:generate
```

**Generated TypeScript:**
```typescript
interface Chat {
  (params?: { chat?: number | string }): Route;
}

Chat().url          // → "/chat"
Chat({ chat: 123 }).url  // → "/chat/123"
```

---

## Controller Refactoring

### **Before (Manual Everything):**

```php
public function index(Request $request)
{
    $chats = Chat::where('user_id', auth()->user()->id)->get();

    $chatId = $request->query('chat_id');  /* Manual extraction */
    $activeChat = null;

    if ($chatId) {
        $activeChat = Chat::find($chatId);  /* Manual fetch */

        if (!$activeChat) {
            abort(404);  /* Manual 404 */
        }

        if ($activeChat->user_id !== auth()->user()->id) {
            abort(403);  /* Manual authorization */
        }
    }

    return Inertia::render(...);
}
```

**Lines of code: 15**

---

### **After (Auto Everything):**

```php
public function index(Request $request, ?Chat $chat = null)
{
    $chats = Chat::where('user_id', auth()->user()->id)->get();

    /* Authorize if chat provided */
    if ($chat) {
        $this->authorize('view', $chat);
    }

    return Inertia::render(...);
}
```

**Lines of code: 7** (53% reduction!)

**What Laravel does automatically:**
- ✅ Extracts ID from `/chat/123`
- ✅ Runs `Chat::find(123)`
- ✅ Returns 404 if not found
- ✅ Injects `Chat` model into method
- ✅ Only authorization check needed

---

## Testing

### **Created:** `tests/Feature/ChatAuthorizationTest.php`

**4 Tests Covering:**

```php
test('users cannot view other users chats', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $chat = Chat::factory()->create(['user_id' => $owner->id]);

    $this->actingAs($intruder)
        ->get("/chat/{$chat->id}")
        ->assertStatus(403);  /* Forbidden */
});

test('users can view their own chats', function () {
    /* ... */
    ->assertStatus(200);  /* OK */
});

test('users cannot delete other users chats', function () {
    /* ... */
    ->assertStatus(403);
});

test('users can delete their own chats', function () {
    /* ... */
    ->assertStatus(302);  /* Redirect after success */
    expect($chat->fresh()->trashed())->toBeTrue();
});
```

**Security Coverage:**
- ✅ Unauthorized view blocked
- ✅ Authorized view allowed
- ✅ Unauthorized delete blocked
- ✅ Authorized delete works
- ✅ Soft delete verified

---

## Files Modified

| File | Type | Changes |
|------|------|---------|
| `app/Policies/ChatPolicy.php` | Created | 5 authorization methods |
| `app/Providers/AppServiceProvider.php` | Modified | Registered ChatPolicy |
| `app/Http/Controllers/ChatController.php` | Modified | Simplified with route binding |
| `routes/web.php` | Modified | Changed to `/chat/{chat?}`, added `.can()` |
| `resources/js/components/chat/Sidebar.vue` | Modified | Wayfinder route params |
| `tests/Feature/ChatAuthorizationTest.php` | Created | 4 security tests |

**Total:** ~150 lines changed, ~50 lines removed (net: -50 lines!)

---

## Benefits Achieved

### **Security**
- ✅ Centralized authorization logic (single source of truth)
- ✅ Automatic enforcement (harder to forget)
- ✅ Tested coverage (regression prevention)
- ✅ Auditable routes (visible security requirements)

### **Code Quality**
- ✅ DRY principle (no repeated checks)
- ✅ Single Responsibility (policies only handle auth)
- ✅ Declarative routes (security visible in routes file)
- ✅ Less boilerplate (53% code reduction in controllers)

### **Developer Experience**
- ✅ Type hints (IDE autocomplete for `?Chat $chat`)
- ✅ Auto 404 (no manual checks)
- ✅ RESTful URLs (cleaner, more semantic)
- ✅ Wayfinder integration (type-safe routing)

---

## Key Concepts Learned

### **1. Laravel Policies**
- Centralize authorization logic per model
- Methods named after abilities (view, update, delete)
- Return true/false for permission
- Registered in `AppServiceProvider`

### **2. Route Middleware Authorization**
- `->can('ability', 'model')` at route level
- Runs before controller (early rejection)
- Automatic 403 on failure
- Declarative security

### **3. Route Model Binding**
- `/resource/{model}` automatically fetches model
- Automatic 404 if not found
- Type-hinted in controller: `(Model $model)`
- Optional with `?`: `/chat/{chat?}`

### **4. RESTful URL Design**
- Route parameters for resource IDs
- Query parameters for filters/options
- Semantic, cacheable, SEO-friendly
- Standard REST conventions

### **5. Request Validation**
- Created `ChatRequest` for reusable validation
- Keeps controllers thin
- Single responsibility for validation logic

---

## Common Pitfalls & Solutions

### **Pitfall 1: Using Collection::find() Instead of Model::find()**

**Wrong:**
```php
$chats = Chat::where('user_id', $user->id)->get();
$activeChat = $chats->find($chatId);  // ❌ Searches collection only!
```

**Right:**
```php
$activeChat = Chat::find($chatId);  // ✅ Queries database
```

**Why:** Collections only contain filtered results. We need to query ALL chats, then authorize.

---

### **Pitfall 2: Forgetting to Authorize**

**Wrong:**
```php
Route::get('/chat/{chat}', [ChatController::class, 'index']);
/* No ->can() middleware! */
```

**Right:**
```php
Route::get('/chat/{chat}', [ChatController::class, 'index'])
    ->can('view', 'chat');  // ✅ Automatic check
```

---

### **Pitfall 3: Query vs Route Parameters Confusion**

**Don't mix them:**
```php
/* ❌ Confusing: */
Route::get('/chat', ...);
/* URL: /chat?chat_id=123 */

Route::get('/chat/{chat}', ...);
/* URL: /chat/123 */

/* Pick ONE pattern and stick with it! */
```

---

## Production Optimizations

### **1. Policy Caching**

```bash
php artisan optimize
```

Caches policies for faster lookups in production.

---

### **2. Eager Load Relationships**

```php
/* Before authorization, if you need related data: */
$chat = Chat::with('messages', 'user')->findOrFail($id);
$this->authorize('view', $chat);
```

---

### **3. Gate for Complex Logic**

```php
/* For authorization beyond simple model checks: */
Gate::define('manage-team', function (User $user, Team $team) {
    return $user->isAdmin() || $user->teams->contains($team);
});

/* In controller: */
$this->authorize('manage-team', $team);
```

---

### **4. Policy Helpers in Blade/Vue**

**Blade:**
```blade
@can('update', $chat)
    <button>Edit</button>
@endcan
```

**Vue (via Inertia):**
```vue
<button v-if="$page.props.auth.can.update">Edit</button>
```

---

## Next Steps

### **Immediate**
- [ ] Add policies for Message, Attachment models
- [ ] Test authorization in browser (manual testing)
- [ ] Add policy tests for edge cases

### **Future**
- [ ] Implement role-based permissions (admin/moderator)
- [ ] Add team-based access (shared chats)
- [ ] Audit log for authorization failures
- [ ] Rate limit authorization checks

---

## Resources

**Laravel Docs:**
- [Authorization](https://laravel.com/docs/11.x/authorization)
- [Policies](https://laravel.com/docs/11.x/authorization#creating-policies)
- [Route Model Binding](https://laravel.com/docs/11.x/routing#route-model-binding)

**Best Practices:**
- Always use policies for authorization
- Use route model binding for cleaner code
- Test authorization thoroughly
- Make security declarative in routes

---

**Created:** 2025-12-14
**Last Updated:** 2025-12-14
**Status:** Backend ✅ | Tests ✅ | Frontend ✅
**Lines Changed:** ~150
**Code Reduced:** -50 lines (net improvement)
