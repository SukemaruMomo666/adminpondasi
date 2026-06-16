<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class UserStatusNotification extends Notification
{
    use Queueable;

    private $details;

    public function __construct($details)
    {
        $this->details = $details;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title'   => $this->details['title'],
            'message' => $this->details['message'],
            'url'     => $this->details['url'],
            'icon'    => $this->details['icon'],
            'color'   => $this->details['color'],
        ];
    }
}
