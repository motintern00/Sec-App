<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\CheckInRequest;
use App\Http\Requests\Attendance\CheckOutRequest;
use App\Services\AttendanceService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function __construct(
        private AttendanceService $attendanceService,
        private NotificationService $notificationService,
    ) {}

    public function index(): View
    {
        $employee = auth()->user()->employee->load(['department', 'shift']);
        $payload = $this->attendanceService->getEmployeeDetailPayload($employee);

        return view('employee.attendance.index', compact('employee', 'payload'));
    }

    public function status(): JsonResponse
    {
        $employee = auth()->user()->employee;

        return response()->json(
            $this->attendanceService->getEmployeeDetailPayload($employee)
        );
    }

    public function checkIn(CheckInRequest $request): JsonResponse
    {
        $user = $request->user();
        $employee = $user->employee;

        try {
            $attendance = $this->attendanceService->checkIn(
                $employee,
                $user,
                (float) $request->validated('latitude'),
                (float) $request->validated('longitude'),
                $request->file('photo'),
                null,
                $request,
            );

            $this->notificationService->notifyCheckInSuccess(
                $user,
                $attendance->check_in_at->format('H:i'),
                $attendance->status->label(),
            );

            return response()->json([
                'success' => true,
                'message' => 'Check-in berhasil. Selamat bekerja!',
                'data' => $this->attendanceService->getEmployeeDetailPayload($employee),
            ]);
        } catch (\InvalidArgumentException $e) {
            $this->notificationService->notifyAttendanceRejected(
                $employee->name,
                $e->getMessage(),
                'Check-in',
            );

            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function checkOut(CheckOutRequest $request): JsonResponse
    {
        $user = $request->user();
        $employee = $user->employee;

        try {
            $attendance = $this->attendanceService->checkOut(
                $employee,
                $user,
                (float) $request->validated('latitude'),
                (float) $request->validated('longitude'),
                $request->file('photo'),
                null,
                $request,
            );

            $this->notificationService->notifyCheckOutSuccess(
                $user,
                $attendance->check_out_at->format('H:i'),
            );

            return response()->json([
                'success' => true,
                'message' => 'Check-out berhasil. Terima kasih!',
                'data' => $this->attendanceService->getEmployeeDetailPayload($employee),
            ]);
        } catch (\InvalidArgumentException $e) {
            $this->notificationService->notifyAttendanceRejected(
                $employee->name,
                $e->getMessage(),
                'Check-out',
            );

            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
