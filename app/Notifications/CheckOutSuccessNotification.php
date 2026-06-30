<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CheckOutSuccessNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $time,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Check-out Berhasil — '.config('app.name'))
            ->greeting('Halo '.$notifiable->name.',')
            ->line('Check-out Anda berhasil dicatat.')
            ->line('Waktu: '.$this->time)
            ->action('Lihat Riwayat', url('/employee/history'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'check_out_success',
            'title' => 'Check-out Berhasil',
            'message' => 'Absensi pulang berhasil pada '.$this->time,
            'icon' => 'bi-box-arrow-right',
            'color' => 'primary',
        ];
    }
}
