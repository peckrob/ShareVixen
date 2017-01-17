<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class ApprovedNotification extends UserNotification
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
        $this->mail_message->subject(config('app.name', 'ShareVixen') . " Membership Approved")
            ->greeting("Your Membership Was Approved!")
            ->line($notifiable->name . ",")
            ->line('Your membership to ' . config('app.name') . " has been approved. You may now begin participating.");


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
