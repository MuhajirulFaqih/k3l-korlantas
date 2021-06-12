<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class KejadianNotification extends Notification
{
    use Queueable;

    protected $data;
    protected $pesan;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($data, $pesan)
    {
        $this->data = $data;
        $this->pesan = $pesan;
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
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'id' => $this->data->id,
            'pesan' => $this->pesan
        ];
    }

    public function broadcastType() {
        return 'kejadian';
    }
}
