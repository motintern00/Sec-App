<?php

namespace App\Services;

use App\Enums\AttendanceStatus;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;

class ShiftService
{
    public function resolveAttendanceDate(Employee $employee, ?Carbon $now = null): Carbon
    {
        $now = $now ?? now();
        $shift = $employee->shift;

        if (! $shift->spans_next_day) {
            return $now->copy()->startOfDay();
        }

        $currentTime = $now->format('H:i:s');
        $nightEnd = $shift->check_out_time;

        if ($currentTime < $nightEnd) {
            return $now->copy()->subDay()->startOfDay();
        }

        return $now->copy()->startOfDay();
    }

    public function canCheckIn(Employee $employee, ?Carbon $now = null): bool
    {
        return $this->getCheckInBlockReason($employee, $now) === null;
    }

    public function getCheckInBlockReason(Employee $employee, ?Carbon $now = null): ?string
    {
        $now = $now ?? now();
        $shift = $employee->shift;
        $attendanceDate = $this->resolveAttendanceDate($employee, $now);

        $existing = Attendance::query()
            ->where('employee_id', $employee->id)
            ->whereDate('attendance_date', $attendanceDate)
            ->first();

        if ($existing && $existing->check_in_at) {
            return 'Pegawai sudah melakukan check-in hari ini.';
        }

        $checkInStart = $this->buildShiftMoment($attendanceDate, $shift->check_in_time, $shift);
        $toleranceEnd = $this->buildShiftMoment($attendanceDate, $shift->tolerance_time, $shift);

        if ($now->lt($checkInStart)) {
            return 'Pegawai tidak dapat check-in sebelum jam shift dimulai.';
        }

        if ($now->gt($toleranceEnd)) {
            return 'Absensi ditolak karena melewati batas toleransi shift.';
        }

        return null;
    }

    public function determineCheckInStatus(Employee $employee, ?Carbon $now = null): AttendanceStatus
    {
        $now = $now ?? now();
        $shift = $employee->shift;
        $attendanceDate = $this->resolveAttendanceDate($employee, $now);
        $checkInStart = $this->buildShiftMoment($attendanceDate, $shift->check_in_time, $shift);

        if ($now->lte($checkInStart)) {
            return AttendanceStatus::Present;
        }

        return AttendanceStatus::Late;
    }

    public function canCheckOut(Attendance $attendance, ?Carbon $now = null): bool
    {
        return $this->getCheckOutBlockReason($attendance, $now) === null;
    }

    public function getCheckOutBlockReason(Attendance $attendance, ?Carbon $now = null): ?string
    {
        $now = $now ?? now();

        if (! $attendance->check_in_at) {
            return 'Pegawai belum melakukan check-in.';
        }

        if ($attendance->check_out_at) {
            return 'Pegawai sudah melakukan check-out hari ini.';
        }

        $minHours = config('attendance.min_work_hours');
        $checkInAt = $attendance->check_in_at;

        if ($now->lt($checkInAt->copy()->addHours($minHours))) {
            return 'Check-out hanya dapat dilakukan setelah bekerja minimal '.$minHours.' jam.';
        }

        return null;
    }

    public function getEmployeeTodayStatus(Employee $employee, ?Carbon $now = null): string
    {
        $now = $now ?? now();
        $attendanceDate = $this->resolveAttendanceDate($employee, $now);

        $attendance = Attendance::query()
            ->where('employee_id', $employee->id)
            ->whereDate('attendance_date', $attendanceDate)
            ->first();

        if (! $attendance || ! $attendance->check_in_at) {
            return 'not_checked_in';
        }

        if (! $attendance->check_out_at) {
            return 'checked_in';
        }

        return 'checked_out';
    }

    public function buildShiftMoment(Carbon $attendanceDate, string $time, \App\Models\Shift $shift): Carbon
    {
        $moment = $attendanceDate->copy()->setTimeFromTimeString($time);

        if ($shift->spans_next_day && $time < $shift->check_in_time) {
            $moment->addDay();
        }

        return $moment;
    }
}
