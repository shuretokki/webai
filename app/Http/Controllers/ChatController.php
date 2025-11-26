<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Enums\Provider;
use App\Models\Chat;

class ChatController extends Controller
{
    public function test() {
        $chat = Chat::where('user_id', auth()->id())->latest()->first();
        $messages = $chat ? $chat->messages : [];

        return Inertia::render('chat/Test', [
            'messages' => $messages,
            'chatId' => $chat ? $chat->id : null,
        ]);
    }



    public function chat(Request $request) {
        $request->validate([
            'prompt'=>'required|string',
            'chat_id' => 'nullable|exists:chats,id'
        ]);

        $user  = auth()->user();
        $chatId = $request->input('chat_id');
        if ($chatId) {
            $chat = Chat::where('user_id', $user->id)->findOrFail($chatId);
        } else {
            $chat = Chat::create([
                'user_id' => $user->id,
                'title' => 'New Chat',
            ]);
        }

        $response = Prism::text()
            ->using(Provider::Gemini, 'gemini-2.0-flash-lite')
            ->withPrompt($request->input('prompt'))
            ->asText();

        $chat->messages()->create([
            'role' => 'assistant',
            'content' => $response->text,
        ]);

        return back();
    }
}
