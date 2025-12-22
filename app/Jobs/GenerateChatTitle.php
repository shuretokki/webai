<?php

namespace App\Jobs;

use App\Models\Chat;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Facades\Prism;

class GenerateChatTitle implements ShouldQueue
{
    use Queueable;

    public $chat;

    /**
     * Create a new job instance.
     */
    public function __construct(Chat $chat)
    {
        $this->chat = $chat;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $messages = $this->chat->messages()->take(2)->get();
        $conversation = $messages->map(fn ($m) => "{$m->role}: {$m->content}")->join("\n");

        $response = Prism::text()
            ->using(Provider::Groq, 'llama-3.1-8b-instant')
            ->withPrompt("Generate a short, concise title (max 4 words) for this chat conversation. Do not use quotes.\n\nConversation:\n$conversation")
            ->generate();

        $this->chat->update(['title' => $response->text]);
    }
}
