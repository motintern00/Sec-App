<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CheckInSuccessNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $employeeName,
        public string $time,
        public string $status,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Check-in Berhasil — '.config('app.name'))
            ->greeting('Halo '.$notifiable->name.',')
            ->line('Check-in Anda berhasil dicatat.')
            ->line('Waktu: '.$this->time)
            ->line('Status: '.$this->status)
            ->action('Lihat Riwayat', url('/employee/history'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'check_in_success',
            'title' => 'Check-in Berhasil',
            'message' => 'Absensi masuk '.$this->time.' — Status: '.$this->status,
            'icon' => 'bi-check-circle-fill',
            'color' => 'success',
        ];
    }
}
