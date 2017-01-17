<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class WelcomeNotification extends UserNotification
{

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $this->mail_message->subject("Welcome to " . config('app.name') . "!")
            ->greeting("Welcome to " . config('app.name') . "!")
            ->line($notifiable->name . ",")
            ->line("Welcome to the " . config('app.name') . " community. Your membership has
            been created and is awaiting approval. The community administrator has been
            notified. When your membership is approved, you will receive a second email.");

        return $this->mail_message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
