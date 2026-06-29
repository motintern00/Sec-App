<?php

namespace Tests\Feature;

use App\Enums\AttendanceStatus;
use App\Enums\UserRole;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_correct_stats(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-29 10:00:00', 'Asia/Jakarta'));

        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $department = Department::create(['name' => 'Security GPA']);
        $shift = Shift::create([
            'name' => 'Shift 1',
            'check_in_time' => '07:00:00',
            'tolerance_time' => '07:15:00',
            'check_out_time' => '14:00:00',
            'spans_next_day' => false,
        ]);

        $employee1 = Employee::create([
            'name' => 'Timot',
            'department_id' => $department->id,
            'shift_id' => $shift->id,
            'is_active' => true,
        ]);

        $employee2 = Employee::create([
            'name' => 'Timoty',
            'department_id' => $department->id,
            'shift_id' => $shift->id,
            'is_active' => true,
        ]);

        Attendance::create([
            'employee_id' => $employee1->id,
            'attendance_date' => '2026-06-29',
            'check_in_at' => now(),
            'status' => AttendanceStatus::Present,
            'recorded_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Total Pegawai');
        $response->assertSee('2');
        $response->assertSee('Hadir Hari Ini');
        $response->assertSee('1');
    }
}
