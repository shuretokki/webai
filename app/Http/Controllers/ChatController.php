<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Enums\Provider;
use App\Models\Chat;

class ChatController extends Controller
{
    public function index(Request $request) {
        $chats = Chat::where('user_id', auth()->user()->id)
            ->latest()
            ->get();

        $chatId = $request->query('chat_id');
        $activeChat = null;

        if ($chatId) {
            $activeChat = $chats->firstWhere('id', $chatId);
        }


        return Inertia::render('chat/Index', [
            'chats' => $chats,
            'messages' => $activeChat ? $activeChat->messages : [],
            'chatId' => $activeChat ? $activeChat->id : null,
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

        $chat->messages()->create([
            'role' => 'user',
            'content' => $request->input('prompt')
        ]);

        $response = Prism::text()
            ->using(Provider::Gemini, 'gemini-2.5-flash-lite')
            ->withPrompt($request->input('prompt'))
            ->asText();

        $chat->messages()->create([
            'role' => 'assistant',
            'content' => $response->text,
        ]);

        return back();
    }

    public function stream(Request $request) {
        $request->validate([
            'prompt' => 'required|string',
            'chat_id' => 'nullable|exists:chats,id'
        ]);

        $chatId = $request->input('chat_id');

        if ($chatId) {
            $chat = Chat::where('user_id', auth()->user()->id)->findOrFail($chatId);
        } else {
            $chat = Chat::create(['user_id' => auth()->user()->id, 'title' => 'New Chat']);
        }

        $chat->messages()->create([
            'role' => 'user',
            'content' => $request->input('prompt')
        ]);

        return response()->stream(function () use ($chat, $request) {
            $stream = Prism::text()
                ->using(Provider::Gemini, 'gemini-2.5-flash-lite')
                ->withPrompt($request->input('prompt'))
                ->asStream();

            $fullResponse = '';

            foreach ($stream as $chunk) {
                $text = $chunk->delta ?? '';

                if (empty($text))
                    continue;

                $fullResponse .= $text;

                echo "data: " . json_encode(['text' => $text]) . "\n\n";

                if (ob_get_level() > 0) ob_flush();
                flush();


            }

            $chat->messages()->create([
                'role' => 'assistant',
                'content' => $fullResponse,
            ]);

            echo "data: [Done]\n\n";
            if (ob_get_level() > 0) ob_flush();
            flush();
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }
}
