<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(public string $url)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('【自炊サービス】パスワードリセット')
            ->greeting('こんにちは！')
            ->line('パスワードリセットのリクエストを受け付けました。')
            ->action('パスワードをリセットする', $this->url)
            ->line('このリンクは60分間有効です。')
            ->line('心当たりがない場合は、このメールを無視してください。')
            ->salutation('自炊サービス');
    }
}