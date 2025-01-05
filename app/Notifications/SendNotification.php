<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendNotification extends Notification
{
    public $msg, $type, $header, $web_data, $mail_data;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($notification_data)
    {
        $this->web_data = $notification_data['web'] ?? null;
        $this->mail_data = $notification_data['mail'] ?? null;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
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
            'msg' => $this->web_data['msg'],
            'type' => $this->web_data['type'],
            'header' => $this->web_data['header'],
        ];
    }
}
