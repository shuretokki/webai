<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Chat extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'title'];

    /**
     * Boot model events
     */
    protected static function booted(): void
    {
        /**
         * Soft delete messages when chat is soft deleted
         */
        static::deleting(function (Chat $chat) {
            if (! $chat->isForceDeleting()) {
                $chat->messages()->delete();
            }
        });

        /**
         * Force delete attachments and files when chat is force deleted
         */
        static::forceDeleting(function (Chat $chat) {
            $filePaths = $chat->messages()
                ->withTrashed()
                ->with('attachments')
                ->get()
                ->pluck('attachments')
                ->flatten()
                ->pluck('path')
                ->toArray();

            foreach ($filePaths as $path) {
                if ($path && Storage::disk('public')
                    ->exists($path)) {
                    Storage::disk('public')
                        ->delete($path);
                }
            }

            $chat->messages()
                ->withTrashed()
                ->each(function ($message) {
                    $message->attachments()
                        ->withTrashed()
                        ->forceDelete();

                    $message->forceDelete();
                });
        });
    }

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
