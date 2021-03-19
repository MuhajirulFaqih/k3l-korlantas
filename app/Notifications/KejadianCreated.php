<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Kejadian;
use NotificationChannels\Fcm\FcmNotification;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\FcmChannel;
use App\Personil;
use App\Bhabin;

class KejadianCreated extends Notification
{
    use Queueable;

    protected $kejadian;
    private $personil;
    private $bhabin;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Kejadian $kejadian)
    {
        $this->kejadian = $kejadian;
        $this->personil = new Personil();
        $this->bhabin = new Bhabin();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [FcmChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */

    public function toFcm($notifable){
        dd($notifable);
        $penerima = $this->personil->ambilTokenPemilik($this->kejadian->id_user);
        $penerima = $penerima->merge($this->bhabin->ambilTokenPemilik($this->kejadian->id_user));

        return FcmMessage::create()
            ->setTimeToLive(3*3600)
            ->setRegistrationIds($penerima)
            ->setData([
                'id' => $this->kejadian->id,
                'nama' => $this->kejadian->user->nama,
                'foto' => $this->kejadian->user->foto,
                'jabatan' => $this->kejadian->user->jabatan,
                'pesan' => 'Kejadian Baru',
                'lokasi' => $this->kejadian->lokasi,
                'kejadian' => $this->kejadian->kejadian,
                'lat' => $this->kejadian->lat,
                'lng' => $this->kejadian->lng,
            ]);
    }

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
            //
        ];
    }
}
