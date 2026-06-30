<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DailySummaryNotification extends Notification
{
    use Queueable;

    public function __construct(
        public array $stats,
        public string $date,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Ringkasan Absensi '.$this->date.' — '.config('app.name'))
            ->greeting('Ringkasan Harian Admin')
            ->line('Tanggal: '.$this->date)
            ->line('Total Pegawai: '.$this->stats['total_employees'])
            ->line('Hadir: '.$this->stats['hadir_hari_ini'])
            ->line('Terlambat: '.$this->stats['terlambat'])
            ->line('Belum Hadir: '.$this->stats['belum_hadir'])
            ->action('Buka Dashboard', url('/admin/dashboard'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'daily_summary',
            'title' => 'Ringkasan Harian',
            'message' => $this->date.' — Hadir: '.$this->stats['hadir_hari_ini'].', Terlambat: '.$this->stats['terlambat'].', Belum: '.$this->stats['belum_hadir'],
            'icon' => 'bi-bar-chart-fill',
            'color' => 'info',
        ];
    }
}
