<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Services\AttendanceExportService;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function __construct(
        private AttendanceService $attendanceService,
        private AttendanceExportService $exportService,
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

    public function exportCsv(Request $request): StreamedResponse
    {
        return $this->exportService->exportCsv($this->filters($request));
    }

    public function exportXlsx(Request $request): StreamedResponse
    {
        return $this->exportService->exportXlsx($this->filters($request));
    }

    private function filters(Request $request): array
    {
        return [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'employee_id' => $request->input('employee_id'),
            'name' => $request->input('name'),
        ];
    }
}
