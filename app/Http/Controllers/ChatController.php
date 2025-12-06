<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Enums\Provider;
use Prism\Prism\ValueObjects\Messages\AssistantMessage;
use Prism\Prism\ValueObjects\Messages\UserMessage;

class ChatController extends Controller
{
    public function index(Request $request)
    {
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

    public function stream(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string',
            'chat_id' => 'nullable|exists:chats,id',
        ]);

        $chatId = $request->input('chat_id');

        if ($chatId) {
            $chat = Chat::where('user_id', auth()->user()->id)->findOrFail($chatId);
        } else {
            $chat = Chat::create(['user_id' => auth()->user()->id, 'title' => 'New Chat']);
        }

        $history = [];
        if ($chat->exists) {
            $messages = $chat->messages()->oldest()->get();

            foreach ($messages as $msg) {
                if ($msg->role === 'user') {
                    $history[] = new UserMessage($msg->content);
                } else {
                    $history[] = new AssistantMessage($msg->content);
                }
            }
        }

        $history[] = new UserMessage($request->input('prompt'));
        $chat->messages()->create([
            'role' => 'user',
            'content' => $request->input('prompt'),
        ]);

        return response()->stream(function () use ($chat, $request, $history) {
            echo "data: " . json_encode(['chat_id' => $chat->id]) . "\n\n";
            try {
                $stream = Prism::text()
                    ->using(Provider::Gemini, 'gemini-2.5-flash-lite')
                    ->withMessages($history)
                    ->asStream();

                $fullResponse = '';

                foreach ($stream as $chunk) {
                    $text = $chunk->delta ?? '';

                    if (empty($text)) {
                        continue;
                    }

                    $fullResponse .= $text;

                    echo 'data: ' .json_encode(['text' => $text])."\n\n";

                    if (ob_get_level() > 0) {
                        ob_flush();
                    }
                    flush();

                }
            } catch (\Throwable $e) {
                \Log::error($e);
                echo 'data: '.json_encode(['error' => $e->getMessage()])."\n\n";
            }

            $chat->messages()->create([
                'role' => 'assistant',
                'content' => $fullResponse,
            ]);

            if ($chat->title === 'New Chat' || $chat->messages->count() <= 2)
                \App\Jobs\GenerateChatTitle::dispatch($chat);

            echo "data: [Done]\n\n";
            if (ob_get_level() > 0) {
                ob_flush();
            }
            flush();
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    public function update(Request $request, Chat $chat) {
        if ($chat->user_id !== auth()->id())
            abort(403);

        $request->validate([
            'title' => 'required|string|max:255'
        ]);

        $chat->update(['title' => $request->input('title')]);
        return back();
    }
}
