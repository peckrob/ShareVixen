<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class RequestApprovalNotification extends UserNotification
{
    public $user;

    public function __construct($user)
    {
        $this->user = $user;
        parent::__construct();
    }

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
        $this->mail_message->subject(config('app.name', 'ShareVixen') . " Membership Approval Required")
            ->greeting("A new user has joined!")
            ->line($notifiable->name . ",")
            ->line("A new user has joined your " . config('app.name') . " community. This user will need to be approved before they will be allowed to participate.")
            ->line("Name:" . $this->user->name)
            ->line("Email:" . $this->user->email);

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
