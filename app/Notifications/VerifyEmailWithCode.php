<?php

namespace App\Notifications;

use App\Models\EmailVerificationCode;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmailWithCode extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $code
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('verification.email_subject'))
            ->greeting(__('verification.email_greeting'))
            ->line(__('verification.email_intro'))
            ->line("**{$this->code}**")
            ->line(__('verification.email_expiration', ['minutes' => EmailVerificationCode::EXPIRATION_MINUTES]))
            ->line(__('verification.email_ignore'));
    }
}
