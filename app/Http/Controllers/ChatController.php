<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Facades\Prism;
use Prism\Prism\ValueObjects\Media\Image;
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
            'model' => 'nullable|string',
            'files.*' => 'nullable|file|max:10240', // Validate array of files
        ]);

        $model = $request->input('model', 'gemini-2.5-flash-lite');
        $chatId = $request->input('chat_id');

        if ($chatId) {
            $chat = Chat::where('user_id', auth()->user()->id)->findOrFail($chatId);
        } else {
            $chat = Chat::create(['user_id' => auth()->user()->id, 'title' => 'New Chat']);
        }

        $history = [];
        if ($chat->exists) {
            $messages = $chat->messages()->latest()->take(10)->get()->reverse();

            foreach ($messages as $msg) {
                if ($msg->role === 'user') {
                    $history[] = new UserMessage($msg->content);
                } else {
                    $history[] = new AssistantMessage($msg->content);
                }
            }
        }

        $prismContent = [];
        $attachmentsData = [];

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                // 1. Store File
                $path = $file->store('attachments', 'public');

                // 2. Add to DB Data
                $attachmentsData[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ];

                // 3. Add to Prism (AI)
                if (str_starts_with($file->getMimeType(), 'image/')) {
                    $prismContent[] = Image::fromLocalPath($file->getPathname(), $file->getMimeType());
                }
                // TODO: Add PDF/Document support if Prism supports it for Gemini
            }
        }

        $history[] = new UserMessage($request->input('prompt'), $prismContent);

        $message = $chat->messages()->create([
            'role' => 'user',
            'content' => $request->input('prompt'),
        ]);

        if (!empty($attachmentsData)) {
            $message->attachments()->createMany($attachmentsData);
        }

        return response()->stream(function () use ($chat, $history, $model) {
            echo 'data: '.json_encode(['chat_id' => $chat->id])."\n\n";
            try {
                $stream = Prism::text()
                    ->using(Provider::Gemini, $model)
                    ->withMessages($history)
                    ->asStream();

                $fullResponse = '';

                foreach ($stream as $chunk) {
                    $text = $chunk->delta ?? '';

                    if (empty($text)) {
                        continue;
                    }

                    $fullResponse .= $text;

                    echo 'data: '.json_encode(['text' => $text])."\n\n";

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

            if ($chat->title === 'New Chat' || $chat->messages->count() <= 2) {
                \App\Jobs\GenerateChatTitle::dispatch($chat);
            }

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

    public function update(Request $request, Chat $chat)
    {
        if ($chat->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $chat->update(['title' => $request->input('title')]);

        return back();
    }

    public function destroy(Chat $chat)
    {
        $this->authorize('delete', $chat);

        $chat->delete();
        $previousUrl = url()->previous();
        $atDeleted = str_contains($previousUrl, 'chat_id'.$chat->id);

        return $atDeleted ? to_route('chat') : back();
    }
}
