<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateChatRequest;
use App\Http\Resources\MessageResource;
use App\Models\Chat;
use App\Models\UserUsage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Facades\Prism;
use Prism\Prism\ValueObjects\Media\Image;
use Prism\Prism\ValueObjects\Messages\AssistantMessage;
use Prism\Prism\ValueObjects\Messages\UserMessage;

use function strlen;

class ChatController extends Controller
{
    public function index(Request $request, ?Chat $chat = null)
    {
        $chats = Chat::where(
            'user_id', auth()->user()->id)
            ->latest()
            ->get();

        if ($chat) {
            $this->authorize('view', $chat);
        }

        return Inertia::render('chat/Index', [
            'chats' => $chats,
            'messages' => $chat
                ? MessageResource::collection(
                    $chat->messages()
                        ->with('attachments')
                        ->get())
                : [],
            'chatId' => $chat?->id,
        ]);
    }

    public function stream(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string',
            'chat_id' => 'nullable|exists:chats,id',
            'model' => 'nullable|string',
            'files.*' => 'nullable|file|max:10240',
        ]);

        $model = $request->input('model', 'gemini-2.5-flash-lite');
        $chatId = $request->input('chat_id');
        $user = auth()->user();

        if ($user->hasExceededQuota('messages', 100)) {
            return response()->json([
                'error' => 'You have reached your monthly message limit. Upgrade your plan to increase limit.',
            ], 403);
        }

        if ($chatId) {
            $chat = Chat::where('user_id', $user->id)
                ->findOrFail($chatId);
        } else {
            $chat = Chat::create(['user_id' => $user->id, 'title' => 'New Chat']);
        }

        $history = [];
        if ($chat->exists) {
            $messages = $chat->messages()
                ->latest()
                ->take(10)
                ->get()
                ->reverse();

            foreach ($messages as $msg) {
                $history[] = ($msg->role === 'user')
                    ? new UserMessage($msg->content)
                    : new AssistantMessage($msg->content);
            }
        }

        $prismContent = [];
        $attachmentsData = [];

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('attachments', 'public');
                $attachmentsData[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ];

                if (str_starts_with($file->getMimeType(), 'image/')) {
                    $prismContent[] = Image::fromLocalPath($file->getPathname(), $file->getMimeType());
                }

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
                // TODO: Add PDF/Document support if Prism supports it for Gemini
            }
        }

        $history[] = new UserMessage($request->input('prompt'), $prismContent);

        $message = $chat->messages()->create([
            'role' => 'user',
            'content' => $request->input('prompt'),
        ]);

        if (! empty($attachmentsData)) {
            $message->attachments()->createMany($attachmentsData);
        }

        UserUsage::record(
            userId: $user->id,
            type: 'message_sent',
            messages: 1,
            metadata: [
                'chat_id' => $chat->id,
                'model' => $model,
                'has_attachments' => ! empty($attachmentsData),
            ]
        );

        return response()->stream(function () use ($chat, $history, $model, $user) {
            echo 'data: '.json_encode(['chat_id' => $chat->id])."\n\n";
            try {
                $stream = Prism::text()
                    ->using(Provider::Gemini, $model)
                    ->withMessages($history)
                    ->asStream();

                $fullResponse = '';
                $totalTokens = 0;

                foreach ($stream as $chunk) {
                    $text = $chunk->delta ?? '';

                    if (empty($text)) {
                        continue;
                    }

                    $fullResponse .= $text;
                    $totalTokens += (int) (strlen($text) / 4);

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

    public function update(UpdateChatRequest $request, Chat $chat)
    {
        $chat->update([
            'title' => $request
                ->validated()
                ->input('title')]);

        return back();
    }

    public function destroy(Chat $chat)
    {
        $chat->delete();

        $atDeleted = str_contains(
            url()->previous(), "chat_id{$chat->id}");

        return $atDeleted ? to_route('chat') : back();
    }
}
