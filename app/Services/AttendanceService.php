<?php

namespace App\Services;

use App\Enums\AttendanceStatus;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttendanceService
{
    public function __construct(
        private GeolocationService $geolocationService,
        private ShiftService $shiftService,
    ) {}

    public function checkIn(
        Employee $employee,
        User $recorder,
        float $latitude,
        float $longitude,
        UploadedFile $photo,
        ?Carbon $now = null
    ): Attendance {
        $now = $now ?? now();

        if (! $employee->is_active) {
            throw new \InvalidArgumentException('Pegawai tidak aktif.');
        }

        if (! $this->geolocationService->isWithinOfficeRadius($latitude, $longitude)) {
            throw new \InvalidArgumentException('Anda berada di luar area kantor. Absensi ditolak.');
        }

        $blockReason = $this->shiftService->getCheckInBlockReason($employee, $now);
        if ($blockReason) {
            throw new \InvalidArgumentException($blockReason);
        }

        $attendanceDate = $this->shiftService->resolveAttendanceDate($employee, $now);
        $status = $this->shiftService->determineCheckInStatus($employee, $now);
        $photoPath = $this->storePhoto($photo);

        return DB::transaction(function () use ($employee, $recorder, $latitude, $longitude, $photoPath, $attendanceDate, $status, $now) {
            $attendance = Attendance::query()->firstOrNew([
                'employee_id' => $employee->id,
                'attendance_date' => $attendanceDate,
            ]);

            if ($attendance->check_in_at) {
                throw new \InvalidArgumentException('Pegawai sudah melakukan check-in hari ini.');
            }

            $attendance->fill([
                'check_in_at' => $now,
                'status' => $status,
                'check_in_photo' => $photoPath,
                'check_in_latitude' => $latitude,
                'check_in_longitude' => $longitude,
                'recorded_by' => $recorder->id,
            ]);
            $attendance->save();

            return $attendance->fresh(['employee.department', 'employee.shift']);
        });
    }

    public function checkOut(
        Employee $employee,
        User $recorder,
        float $latitude,
        float $longitude,
        UploadedFile $photo,
        ?Carbon $now = null
    ): Attendance {
        $now = $now ?? now();

        if (! $this->geolocationService->isWithinOfficeRadius($latitude, $longitude)) {
            throw new \InvalidArgumentException('Anda berada di luar area kantor. Absensi ditolak.');
        }

        $attendanceDate = $this->shiftService->resolveAttendanceDate($employee, $now);

        $attendance = Attendance::query()
            ->where('employee_id', $employee->id)
            ->whereDate('attendance_date', $attendanceDate)
            ->first();

        if (! $attendance) {
            throw new \InvalidArgumentException('Pegawai belum melakukan check-in.');
        }

        $blockReason = $this->shiftService->getCheckOutBlockReason($attendance, $now);
        if ($blockReason) {
            throw new \InvalidArgumentException($blockReason);
        }

        $photoPath = $this->storePhoto($photo);

        $attendance->update([
            'check_out_at' => $now,
            'check_out_photo' => $photoPath,
            'check_out_latitude' => $latitude,
            'check_out_longitude' => $longitude,
            'recorded_by' => $recorder->id,
        ]);

        return $attendance->fresh(['employee.department', 'employee.shift']);
    }

    public function getEmployeeDetailPayload(Employee $employee, ?Carbon $now = null): array
    {
        $now = $now ?? now();
        $employee->load(['department', 'shift']);
        $todayStatus = $this->shiftService->getEmployeeTodayStatus($employee, $now);
        $attendanceDate = $this->shiftService->resolveAttendanceDate($employee, $now);

        $attendance = Attendance::query()
            ->where('employee_id', $employee->id)
            ->whereDate('attendance_date', $attendanceDate)
            ->first();

        $canCheckIn = $todayStatus === 'not_checked_in'
            && $this->shiftService->canCheckIn($employee, $now);
        $canCheckOut = $attendance
            && $this->shiftService->canCheckOut($attendance, $now);

        $actionLabel = 'Absen Masuk';
        if ($canCheckOut) {
            $actionLabel = 'Absen Pulang';
        }

        $checkInReason = $todayStatus === 'not_checked_in'
            ? $this->shiftService->getCheckInBlockReason($employee, $now)
            : null;

        return [
            'id' => $employee->id,
            'name' => $employee->name,
            'department' => $employee->department->name,
            'shift' => [
                'name' => $employee->shift->name,
                'check_in' => substr($employee->shift->check_in_time, 0, 5),
                'tolerance' => substr($employee->shift->tolerance_time, 0, 5),
                'check_out' => substr($employee->shift->check_out_time, 0, 5),
            ],
            'today_status' => $todayStatus,
            'can_check_in' => $canCheckIn,
            'can_check_out' => (bool) $canCheckOut,
            'action_label' => $actionLabel,
            'status_message' => $checkInReason ?? $this->getStatusMessage($todayStatus, $attendance, $now),
        ];
    }

    public function getFiltered(array $filters): LengthAwarePaginator
    {
        $query = Attendance::query()
            ->with(['employee.department', 'employee.shift', 'recorder'])
            ->orderByDesc('attendance_date')
            ->orderByDesc('check_in_at');

        if (! empty($filters['start_date'])) {
            $query->whereDate('attendance_date', '>=', $filters['start_date']);
        }

        if (! empty($filters['end_date'])) {
            $query->whereDate('attendance_date', '<=', $filters['end_date']);
        }

        if (! empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (! empty($filters['name'])) {
            $query->whereHas('employee', function ($q) use ($filters) {
                $q->where('name', 'like', '%'.$filters['name'].'%');
            });
        }

        return $query->paginate(15)->withQueryString();
    }

    public function getTodayForDashboard(?Carbon $date = null): \Illuminate\Database\Eloquent\Collection
    {
        $date = $date ?? now();

        return Attendance::query()
            ->with(['employee.department', 'employee.shift'])
            ->whereDate('attendance_date', $date)
            ->orderByDesc('check_in_at')
            ->limit(10)
            ->get();
    }

    private function storePhoto(UploadedFile $photo): string
    {
        $disk = config('attendance.photo_disk');
        $basePath = config('attendance.photo_path');
        $directory = $basePath.'/'.now()->format('Y/m');
        $filename = Str::uuid().'.'.$photo->getClientOriginalExtension();

        Storage::disk($disk)->putFileAs($directory, $photo, $filename);

        return $directory.'/'.$filename;
    }

    private function getStatusMessage(string $todayStatus, ?Attendance $attendance, Carbon $now): string
    {
        if ($todayStatus === 'checked_in' && $attendance) {
            $blockReason = $this->shiftService->getCheckOutBlockReason($attendance, $now);
            if ($blockReason) {
                return $blockReason;
            }

            return 'Pegawai sudah check-in. Siap untuk check-out.';
        }

        if ($todayStatus === 'checked_out') {
            return 'Pegawai sudah menyelesaikan absensi hari ini.';
        }

        return 'Pilih pegawai untuk melihat status absensi.';
    }
}
