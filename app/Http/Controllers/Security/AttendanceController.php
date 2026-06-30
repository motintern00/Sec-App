<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\CheckInRequest;
use App\Http\Requests\Attendance\CheckOutRequest;
use App\Models\Employee;
use App\Services\AttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function __construct(
        private AttendanceService $attendanceService,
    ) {}

    public function index(): View
    {
        $employees = Employee::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('security.attendance.index', compact('employees'));
    }

    public function employeeDetail(Employee $employee): JsonResponse
    {
        if (! $employee->is_active) {
            return response()->json(['message' => 'Pegawai tidak aktif.'], 422);
        }

        return response()->json(
            $this->attendanceService->getEmployeeDetailPayload($employee)
        );
    }

    public function checkIn(CheckInRequest $request): JsonResponse
    {
        try {
            $employee = Employee::findOrFail($request->validated('employee_id'));
            $attendance = $this->attendanceService->checkIn(
                $employee,
                $request->user(),
                (float) $request->validated('latitude'),
                (float) $request->validated('longitude'),
                $request->file('photo'),
                null,
                $request,
            );

            return response()->json([
                'success' => true,
                'message' => 'Check-in berhasil untuk '.$employee->name.'.',
                'data' => $this->attendanceService->getEmployeeDetailPayload($employee),
                'attendance' => [
                    'check_in_at' => $attendance->check_in_at?->format('H:i'),
                    'status' => $attendance->status->label(),
                ],
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function checkOut(CheckOutRequest $request): JsonResponse
    {
        try {
            $employee = Employee::findOrFail($request->validated('employee_id'));
            $attendance = $this->attendanceService->checkOut(
                $employee,
                $request->user(),
                (float) $request->validated('latitude'),
                (float) $request->validated('longitude'),
                $request->file('photo'),
                null,
                $request,
            );

            return response()->json([
                'success' => true,
                'message' => 'Check-out berhasil untuk '.$employee->name.'.',
                'data' => $this->attendanceService->getEmployeeDetailPayload($employee),
                'attendance' => [
                    'check_out_at' => $attendance->check_out_at?->format('H:i'),
                ],
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
