<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class ActivityLogService
{
    public function log(
        User $user,
        string $action,
        ?Employee $employee = null,
        ?Attendance $attendance = null,
        ?Request $request = null,
        array $metadata = []
    ): ActivityLog {
        return ActivityLog::create([
            'user_id' => $user->id,
            'action' => $action,
            'employee_id' => $employee?->id,
            'attendance_id' => $attendance?->id,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'metadata' => $metadata ?: null,
        ]);
    }

    public function getFiltered(array $filters = []): LengthAwarePaginator
    {
        $query = ActivityLog::query()
            ->with(['user', 'employee', 'attendance'])
            ->orderByDesc('created_at');

        if (! empty($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        if (! empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }

        if (! empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        return $query->paginate(20)->withQueryString();
    }
}
