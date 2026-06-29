<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Shift;
use App\Models\User;
use App\Services\ShiftService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShiftServiceTest extends TestCase
{
    use RefreshDatabase;

    private ShiftService $shiftService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->shiftService = app(ShiftService::class);
        $this->seedTestData();
    }

    private function seedTestData(): void
    {
        $department = Department::create(['name' => 'Security GPA']);
        $shift1 = Shift::create([
            'name' => 'Shift 1',
            'check_in_time' => '07:00:00',
            'tolerance_time' => '07:15:00',
            'check_out_time' => '14:00:00',
            'spans_next_day' => false,
        ]);

        Employee::create([
            'name' => 'Test Employee',
            'department_id' => $department->id,
            'shift_id' => $shift1->id,
            'is_active' => true,
        ]);
    }

    public function test_can_check_in_within_tolerance_window(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-29 07:10:00', 'Asia/Jakarta'));
        $employee = Employee::first();

        $this->assertTrue($this->shiftService->canCheckIn($employee));
        $this->assertEquals('late', $this->shiftService->determineCheckInStatus($employee)->value);
    }

    public function test_cannot_check_in_before_shift_start(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-29 06:30:00', 'Asia/Jakarta'));
        $employee = Employee::first();

        $this->assertFalse($this->shiftService->canCheckIn($employee));
        $this->assertStringContainsString('sebelum jam shift', $this->shiftService->getCheckInBlockReason($employee));
    }

    public function test_cannot_check_in_after_tolerance(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-29 07:20:00', 'Asia/Jakarta'));
        $employee = Employee::first();

        $this->assertFalse($this->shiftService->canCheckIn($employee));
        $this->assertStringContainsString('toleransi', $this->shiftService->getCheckInBlockReason($employee));
    }

    public function test_night_shift_attendance_date_before_midnight_end(): void
    {
        $department = Department::first();
        $nightShift = Shift::create([
            'name' => 'Shift 3',
            'check_in_time' => '21:00:00',
            'tolerance_time' => '21:15:00',
            'check_out_time' => '04:00:00',
            'spans_next_day' => true,
        ]);

        $employee = Employee::create([
            'name' => 'Night Worker',
            'department_id' => $department->id,
            'shift_id' => $nightShift->id,
            'is_active' => true,
        ]);

        Carbon::setTestNow(Carbon::parse('2026-06-30 02:00:00', 'Asia/Jakarta'));
        $date = $this->shiftService->resolveAttendanceDate($employee);

        $this->assertEquals('2026-06-29', $date->toDateString());
    }
}
