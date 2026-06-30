<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\User;
use App\Notifications\AttendanceRejectedNotification;
use App\Notifications\CheckInSuccessNotification;
use App\Notifications\CheckOutSuccessNotification;

class NotificationService
{
    public function notifyCheckInSuccess(User $user, string $time, string $status): void
    {
        $user->notify(new CheckInSuccessNotification($user->name, $time, $status));
    }

    public function notifyCheckOutSuccess(User $user, string $time): void
    {
        $user->notify(new CheckOutSuccessNotification($time));
    }

    public function notifyAttendanceRejected(string $employeeName, string $reason, string $action): void
    {
        User::query()->where('role', UserRole::Admin)->each(function (User $admin) use ($employeeName, $reason, $action) {
            $admin->notify(new AttendanceRejectedNotification($employeeName, $reason, $action));
        });
    }
}
