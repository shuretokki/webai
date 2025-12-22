<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class VerifyCurrentEmailForChange extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $newEmail,
        public string $token
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = URL::temporarySignedRoute(
            'profile.email.verify-current',
            now()->addMinutes(60),
            ['token' => $this->token]
        );

        return (new MailMessage)
            ->subject('Verify Email Change Request')
            ->line('We received a request to change your email address to: **' . $this->newEmail . '**')
            ->line('If you made this request, please click the button below to verify this change.')
            ->action('Verify Email Change', $verificationUrl)
            ->line('If you did not request this change, please ignore this email and your email will remain unchanged.')
            ->line('This link will expire in 60 minutes.');
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
