<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AttendanceRejectedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $employeeName,
        public string $reason,
        public string $action,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'attendance_rejected',
            'title' => 'Absensi Ditolak',
            'message' => $this->employeeName.' — '.$this->action.': '.$this->reason,
            'icon' => 'bi-exclamation-triangle-fill',
            'color' => 'danger',
        ];
    }
}
