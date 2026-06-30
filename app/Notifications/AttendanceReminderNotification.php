<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AttendanceReminderNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $shiftName,
        public string $toleranceTime,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'attendance_reminder',
            'title' => 'Reminder Absensi',
            'message' => 'Segera absen! Batas toleransi '.$this->shiftName.' pukul '.$this->toleranceTime,
            'icon' => 'bi-alarm',
            'color' => 'warning',
        ];
    }
}
