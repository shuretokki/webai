<?php

namespace App\Policies;

use App\Models\Chat;
use App\Models\User;

class ChatPolicy
{
    /**
     * Determine if user can view the chat.
     *
     * Users can only view their own chats.
     */
    public function view(User $user, Chat $chat): bool
    {
        return $user->id === $chat->user_id;
    }

    /**
     * Determine if user can update the chat.
     *
     * Users can only update their own chats.
     */
    public function update(User $user, Chat $chat): bool
    {
        return $user->id === $chat->user_id;
    }

    /**
     * Determine if user can delete the chat.
     *
     * Users can only delete their own non-trashed chats.
     */
    public function delete(User $user, Chat $chat): bool
    {
        return $user->id === $chat->user_id && ! $chat->trashed();
    }

    /**
     * Determine if user can restore the chat.
     *
     * Users can only restore their own soft-deleted chats.
     */
    public function restore(User $user, Chat $chat): bool
    {
        return $user->id === $chat->user_id && $chat->trashed();
    }

    /**
     * Determine if user can permanently delete the chat.
     *
     * Users can only force delete their own chats.
     */
    public function forceDelete(User $user, Chat $chat): bool
    {
        return $user->id === $chat->user_id;
    }
}
