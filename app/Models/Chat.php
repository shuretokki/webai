<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chat extends Model
{
    protected $fillable = ['user_id', 'title'];

    /**
     * Summary of user
     *
     * @return BelongsTo<User, Chat>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Summary of messages
     *
     * @return HasMany<Message, Chat>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}
