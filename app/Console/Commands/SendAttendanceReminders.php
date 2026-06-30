<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Notifications\AttendanceReminderNotification;
use App\Services\ShiftService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendAttendanceReminders extends Command
{
    protected $signature = 'attendance:send-reminders';

    protected $description = 'Kirim reminder absensi 15 menit sebelum toleransi habis';

    public function handle(ShiftService $shiftService): int
    {
        $now = now();
        $employees = Employee::query()->where('is_active', true)->with(['shift', 'user'])->get();

        foreach ($employees as $employee) {
            if (! $employee->user) {
                continue;
            }

            $shift = $employee->shift;
            $attendanceDate = $shiftService->resolveAttendanceDate($employee, $now);
            $toleranceEnd = $shiftService->buildShiftMoment($attendanceDate, $shift->tolerance_time, $shift);
            $reminderAt = $toleranceEnd->copy()->subMinutes(15);

            if ($now->format('Y-m-d H:i') !== $reminderAt->format('Y-m-d H:i')) {
                continue;
            }

            if ($shiftService->getEmployeeTodayStatus($employee, $now) !== 'not_checked_in') {
                continue;
            }

            if (! $shiftService->canCheckIn($employee, $now)) {
                continue;
            }

            $alreadySent = $employee->user->notifications()
                ->where('created_at', '>=', $now->copy()->startOfDay())
                ->where('data->type', 'attendance_reminder')
                ->exists();

            if ($alreadySent) {
                continue;
            }

            $employee->user->notify(new AttendanceReminderNotification(
                $shift->name,
                substr($shift->tolerance_time, 0, 5),
            ));
        }

        return self::SUCCESS;
    }
}
