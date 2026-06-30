<?php

namespace App\Services;

use App\Enums\AttendanceStatus;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Collection;

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

    public function getChartData(int $days = 7): array
    {
        $labels = [];
        $tepatWaktu = [];
        $terlambat = [];
        $belumHadir = [];

        $totalActive = \App\Models\Employee::query()->where('is_active', true)->count();

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateString = $date->toDateString();
            $labels[] = $date->format('d M');

            $records = Attendance::query()
                ->whereDate('attendance_date', $dateString)
                ->whereNotNull('check_in_at')
                ->get();

            $tepatWaktu[] = $records->where('status', AttendanceStatus::Present)->count();
            $terlambat[] = $records->where('status', AttendanceStatus::Late)->count();
            $hadir = $tepatWaktu[count($tepatWaktu) - 1] + $terlambat[count($terlambat) - 1];
            $belumHadir[] = max(0, $totalActive - $hadir);
        }

        return compact('labels', 'tepatWaktu', 'terlambat', 'belumHadir');
    }

    public function getAbsentEmployeesToday(?Carbon $date = null): Collection
    {
        $date = $date ?? now();
        $dateString = $date->toDateString();

        $checkedInIds = Attendance::query()
            ->whereDate('attendance_date', $dateString)
            ->whereNotNull('check_in_at')
            ->pluck('employee_id')
            ->all();

        return Employee::query()
            ->where('is_active', true)
            ->with(['department', 'shift'])
            ->orderBy('name')
            ->get()
            ->filter(function (Employee $employee) use ($date, $dateString, $checkedInIds) {
                if (in_array($employee->id, $checkedInIds, true)) {
                    return false;
                }

                return $this->shiftService->resolveAttendanceDate($employee, $date)->toDateString() === $dateString;
            })
            ->values();
    }
}
