<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChatRequest;
use App\Http\Requests\UpdateChatRequest;
use App\Http\Resources\MessageResource;
use App\Models\Chat;
use App\Models\UserUsage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Facades\Prism;
use Prism\Prism\ValueObjects\Media\Image;
use Prism\Prism\ValueObjects\Messages\AssistantMessage;
use Prism\Prism\ValueObjects\Messages\UserMessage;

class ChatController extends Controller
{
    public function index(Request $request, ?Chat $chat = null)
    {
        $chats = Chat::where(
            'user_id', auth()->user()->id)
            ->latest()
            ->get()
            ->map(fn ($c) => [
                'id' => $c->getRouteKey(),
                'title' => $c->title,
                'created_at' => $c->created_at,
                'updated_at' => $c->updated_at,
            ]);

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
            'chatId' => $chat?->getRouteKey(),
        ]);
    }

    public function stream(ChatRequest $request)
    {
        $modelId = $request->input('model', 'gemini-2.5-flash');
        $models = config('ai.models');
        $modelConfig = collect($models)->firstWhere('id', $modelId);

        if (! $modelConfig) {
            return response()->json(['error' => 'Invalid model selected.'], 400);
        }

        $user = auth()->user();

        if (! $modelConfig['is_free'] && ! in_array($user->subscription_tier, ['plus', 'enterprise'])) {
            return response()->json(
                ['error' => 'This model requires a Plus or Enterprise subscription.'],
                403
            );
        }

        if ($user->hasExceededQuota('messages', config('limits.usage.daily_token_limit'))) {
            return response()->json([
                'error' => 'You have reached your monthly message limit. Upgrade your plan to increase limit.',
            ], 403);
        }

        $chatId = $request->input('chat_id');

        if ($chatId) {
            $decodedId = \Vinkla\Hashids\Facades\Hashids::decode($chatId);
            $chatId = ! empty($decodedId) ? $decodedId[0] : $chatId;
        }

        $chat = $chatId
            ? Chat::where('user_id', $user->id)
                ->findOrFail($chatId)
            : Chat::create([
                'user_id' => $user->id,
                'title' => 'New Chat']);

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
            }
        }

        $history[] = new UserMessage($request->input('prompt'), $prismContent);

        $message = $chat
            ->messages()
            ->create([
                'role' => 'user',
                'content' => $request->input('prompt'),
            ]);

        if (! empty($attachmentsData)) {
            $message
                ->attachments()
                ->createMany($attachmentsData);
        }

        UserUsage::record(
            userId: $user->id,
            type: 'message_sent',
            messages: 1,
            metadata: [
                'chat_id' => $chat->getRouteKey(),
                'model' => $modelId,
                'has_attachments' => ! empty($attachmentsData),
            ]
        );

        return response()->stream(function () use ($chat, $history, $modelId, $user, $modelConfig) {
            echo 'data: '.json_encode(['chat_id' => $chat->getRouteKey()])."\n\n";

            $fullResponse = '';
            $inputTokens = 0;
            $outputTokens = 0;
            $totalTokens = 0;

            if (! $modelConfig['is_free']) {
                $fullResponse = "Model usage under progress.\n\n(Simulated response for {$modelConfig['name']})";
                $inputTokens = 25;
                $outputTokens = 25;
                $totalTokens = 50;

                $words = explode(' ', $fullResponse);
                foreach ($words as $word) {
                    $text = $word.' ';
                    echo 'data: '.json_encode(['text' => $text])."\n\n";
                    if (ob_get_level() > 0) {
                        ob_flush();
                    }
                    flush();
                    usleep(50000);
                }
            } else {
                try {
                    $provider = match ($modelConfig['provider']) {
                        'gemini' => Provider::Gemini,
                        'openai' => Provider::OpenAI,
                        'anthropic' => Provider::Anthropic,
                        'xai' => Provider::XAI,
                        'deepseek' => Provider::DeepSeek,
                        'groq' => Provider::Groq,
                        default => Provider::Gemini,
                    };

                    $stream = Prism::text()
                        ->using($provider, $modelId)
                        ->withSystemPrompt(
                            'You are a helpful AI assistant. You can reason about the user request. If you do reason, you MUST wrap your thinking process in <think> tags like this: <think>my thought process</think>. Then provide your final answer.'
                        )
                        ->withMessages($history)
                        ->asStream();

                    $lastChunk = null;

                    foreach ($stream as $chunk) {
                        $lastChunk = $chunk;

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

                    if ($lastChunk && isset($lastChunk->usage)) {
                        $inputTokens = $lastChunk->usage->promptTokens ?? 0;
                        $outputTokens = $lastChunk->usage->completionTokens ?? 0;
                        $totalTokens = $inputTokens + $outputTokens;
                    } else {
                        $inputTokens = (int) (array_sum(array_map(fn ($msg) => strlen($msg->content ?? ''), $history)) / 4);
                        $outputTokens = (int) (strlen($fullResponse) / 4);
                        $totalTokens = $inputTokens + $outputTokens;
                    }
                } catch (\Exception $e) {
                    \Log::error('AI Model Error', [
                        'message' => $e->getMessage(),
                        'model' => $modelId,
                        'provider' => $modelConfig['provider'],
                        'user_id' => $user->id,
                    ]);

                    $errorMessage = $this->getReadableErrorMessage($e, $modelConfig);

                    echo 'data: '.json_encode([
                        'error' => $errorMessage])."\n\n";

                    $inputTokens = (int) (array_sum(array_map(fn ($msg) => strlen($msg->content ?? ''), $history)) / 4);
                    $outputTokens = (int) (strlen($fullResponse) / 4);
                    $totalTokens = $inputTokens + $outputTokens;
                }
            }

            $assistantMessage = $chat->messages()->create([
                'role' => 'assistant',
                'content' => $fullResponse,
            ]);

            event(new \App\Events\MessageSent($chat, $assistantMessage));

            UserUsage::record(
                userId: $user->id,
                type: 'ai_response',
                tokens: $totalTokens,
                metadata: [
                    'chat_id' => $chat->id,
                    'model' => $modelId,
                    'input_tokens' => $inputTokens,
                    'output_tokens' => $outputTokens,
                    'response_length' => \strlen($fullResponse),
                ]
            );

            if ($chat->title === 'New Chat'
                || $chat->messages->count() <= 2) {
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
            'title' => $request->validated()['title'],
        ]);

        return back();
    }

    public function destroy(Chat $chat)
    {
        $chat->delete();

        $atDeleted = str_contains(
            url()->previous(), "/c/{$chat->getRouteKey()}");

        return $atDeleted
            ? to_route('chat')
            : back();
    }

    public function destroyAll(Request $request)
    {
        $user = auth()->user();

        Chat::where('user_id', $user->id)->delete();

        return response()->json([
            'message' => 'All chats deleted successfully',
        ]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|max:200',
        ]);

        $query = $request->input('q', '');
        $user = auth()->user();

        if (\strlen($query) < 2) {
            return response()->json([
                'results' => [],
            ]);
        }

        $escaped = str_replace(
            ['%', '_'],
            ['\%', '\_'],
            $query);

        $chats = Chat::where(
            'user_id', $user->id)
            ->where(
                'title',
                'LIKE',
                "%{$escaped}%")
            ->whereNull('deleted_at')
            ->limit(5)
            ->get()
            ->map(fn ($chat) => [
                'type' => 'chat',
                'id' => $chat->getRouteKey(),
                'title' => $chat->title,
                'url' => "/c/{$chat->getRouteKey()}",
                'subtitle' => $chat->updated_at->diffForHumans(),
            ]);

        $messages = DB::table('messages')
            ->join(
                'chats',
                'messages.chat_id',
                '=',
                'chats.id')
            ->where('chats.user_id', $user->id)
            ->whereNull('chats.deleted_at')
            ->where(
                'messages.content',
                'LIKE',
                "%{$escaped}%")
            ->whereNull('messages.deleted_at')
            ->select(
                'messages.id',
                'messages.content',
                'chats.title as chat_title',
                'chats.id as chat_id')
            ->limit(10)
            ->get()
            ->map(function ($msg) {
                $hashedChatId = \Vinkla\Hashids\Facades\Hashids::encode($msg->chat_id);

                return [
                    'type' => 'message',
                    'id' => $msg->id,
                    'title' => Str::limit(
                        $msg->content, 60),
                    'url' => "/c/{$hashedChatId}",
                    'subtitle' => "in {$msg->chat_title}",
                ];
            });

        $results = $chats
            ->concat($messages)
            ->take(15);

        return response()
            ->json(['results' => $results]);
    }

    public function export(Chat $chat, string $format = 'md')
    {

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('chat.export', ['chat' => $chat]);

            return $pdf->download("chat-{$chat->id}.pdf");
        }

        return response()->streamDownload(function () use ($chat) {
            echo "# {$chat->title}\n\n";
            echo 'Exported on '.now()->toDateTimeString()."\n\n";

            foreach ($chat->messages as $message) {
                $role = ucfirst($message->role);
                $time = $message->created_at->format('Y-m-d H:i');
                echo "## {$role} - {$time}\n\n";
                echo $message->content."\n\n---\n\n";
            }
        }, "chat-{$chat->id}.md");
    }

    private function getReadableErrorMessage(\Exception $e, array $modelConfig): string
    {
        $message = $e->getMessage();
        $provider = $modelConfig['name'] ?? 'AI model';

        if (str_contains($message, '429') ||
            str_contains($message, 'rate limit') ||
            str_contains($message, 'quota') ||
            str_contains($message, 'Resource has been exhausted')) {
            return "The {$provider} is currently at capacity or rate limited. Please try again in a few moments or switch to a different model.";
        }

        if (str_contains($message, '401') ||
            str_contains($message, '403') ||
            str_contains($message, 'API key') ||
            str_contains($message, 'Unauthorized') ||
            str_contains($message, 'authentication') ||
            str_contains($message, 'invalid_api_key')) {
            return "Invalid or missing API key for {$provider}. Please contact support or try a different model.";
        }

        if (str_contains($message, '404') ||
            str_contains($message, 'not found') ||
            str_contains($message, 'does not exist')) {
            return 'The selected model is not available. Please try a different model.';
        }

        if (str_contains($message, 'content_policy') ||
            str_contains($message, 'safety') ||
            str_contains($message, 'inappropriate')) {
            return 'Your request was blocked by content safety filters. Please rephrase your message.';
        }

        if (str_contains($message, 'context_length') ||
            str_contains($message, 'token limit') ||
            str_contains($message, 'maximum context')) {
            return 'Your conversation is too long for this model. Please start a new chat or use a model with larger context.';
        }

        if (str_contains($message, '500') ||
            str_contains($message, '502') ||
            str_contains($message, '503') ||
            str_contains($message, 'server error') ||
            str_contains($message, 'Internal server error')) {
            return "The {$provider} is experiencing technical difficulties. Please try again later or switch to a different model.";
        }

        if (str_contains($message, 'timeout') ||
            str_contains($message, 'timed out') ||
            str_contains($message, 'network') ||
            str_contains($message, 'connection')) {
            return "Network timeout connecting to {$provider}. Please check your connection and try again.";
        }

        return "An error occurred with {$provider}: ".(strlen($message) > 100 ? substr($message, 0, 100).'...' : $message);
    }
}
