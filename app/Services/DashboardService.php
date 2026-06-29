<?php

namespace App\Services;

use App\Enums\AttendanceStatus;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;

class DashboardService
{
    public function __construct(
        private ShiftService $shiftService,
    ) {}

    public function getStats(?Carbon $date = null): array
    {
        $date = $date ?? now();
        $dateString = $date->toDateString();

        $totalEmployees = Employee::query()->where('is_active', true)->count();

        $attendancesToday = Attendance::query()
            ->whereDate('attendance_date', $dateString)
            ->whereNotNull('check_in_at')
            ->get();

        $presentCount = $attendancesToday->where('status', AttendanceStatus::Present)->count();
        $lateCount = $attendancesToday->where('status', AttendanceStatus::Late)->count();
        $hadirCount = $presentCount + $lateCount;

        $checkedInEmployeeIds = $attendancesToday->pluck('employee_id')->all();

        $activeEmployees = Employee::query()
            ->where('is_active', true)
            ->with('shift')
            ->get();

        $belumHadir = 0;
        foreach ($activeEmployees as $employee) {
            if (in_array($employee->id, $checkedInEmployeeIds, true)) {
                continue;
            }

            $attendanceDate = $this->shiftService->resolveAttendanceDate($employee, $date);
            if ($attendanceDate->toDateString() !== $dateString) {
                continue;
            }

            $belumHadir++;
        }

        return [
            'total_employees' => $totalEmployees,
            'hadir_hari_ini' => $hadirCount,
            'terlambat' => $lateCount,
            'belum_hadir' => $belumHadir,
        ];
    }
}
