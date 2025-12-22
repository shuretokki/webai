<?php

namespace App\Jobs;

use App\Mail\ContactFormSubmitted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class ProcessContactForm implements ShouldQueue
{
    use Queueable;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying.
     */
    public $backoff = [30, 60, 120];

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $name,
        public string $email,
        public string $message,
        public ?string $company = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $adminEmail = config('mail.from.address');
        Mail::to($adminEmail)->send(
            new ContactFormSubmitted(
                $this->name,
                $this->email,
                $this->message,
                $this->company
            )
        );

        \Log::info('Contact form processed', [
            'name' => $this->name,
            'email' => $this->email,
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('Contact form job failed', [
            'name' => $this->name,
            'email' => $this->email,
            'error' => $exception->getMessage(),
        ]);
    }
}
