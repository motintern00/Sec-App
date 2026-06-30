<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function __construct(
        private ActivityLogService $activityLogService,
    ) {}

    public function index(Request $request): View
    {
        $logs = $this->activityLogService->getFiltered([
            'action' => $request->input('action'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ]);

        return view('admin.activity-logs.index', compact('logs'));
    }
}
