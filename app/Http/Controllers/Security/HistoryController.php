<?php

namespace App\Http\Controllers\Security;

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
        $attendances = $this->attendanceService->getFiltered([]);

        return view('security.history.index', compact('attendances'));
    }
}
