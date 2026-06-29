<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function __construct(
        private AttendanceService $attendanceService,
    ) {}

    public function index(Request $request): View
    {
        $attendances = $this->attendanceService->getFiltered([
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'employee_id' => $request->input('employee_id'),
            'name' => $request->input('name'),
        ]);

        $employees = Employee::query()->orderBy('name')->get();

        return view('admin.attendances.index', compact('attendances', 'employees'));
    }

    public function show(Attendance $attendance): View
    {
        $attendance->load(['employee.department', 'employee.shift', 'recorder']);

        return view('admin.attendances.show', compact('attendance'));
    }
}
