<?php

use App\Models\Chat;
use Illuminate\Support\Facades\Broadcast;

/**
 * Authorize user to listen to their own user channel
 */
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

/**
 * Authorize user to listen to specific chat channel
 * Only the chat owner can subscribe to receive message updates
 */
Broadcast::channel('chats.{chatId}', function ($user, $chatId) {
    $chat = Chat::find($chatId);

    return $chat && $chat->user_id === $user->id;
});
