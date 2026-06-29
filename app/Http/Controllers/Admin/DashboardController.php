<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AttendanceService;
use App\Services\DashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService,
        private AttendanceService $attendanceService,
    ) {}

    public function index(): View
    {
        $stats = $this->dashboardService->getStats();
        $todayAttendances = $this->attendanceService->getTodayForDashboard();

        return view('admin.dashboard', compact('stats', 'todayAttendances'));
    }
}
