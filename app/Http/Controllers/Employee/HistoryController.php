<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Services\AttendanceService;
use Illuminate\View\View;

class HistoryController extends Controller
{
    public function __construct(
        private AttendanceService $attendanceService,
    ) {}

    public function index(): View
    {
        $employeeId = auth()->user()->employee_id;

        $attendances = $this->attendanceService->getFiltered([
            'employee_id' => $employeeId,
        ]);

        return view('employee.history.index', compact('attendances'));
    }
}
