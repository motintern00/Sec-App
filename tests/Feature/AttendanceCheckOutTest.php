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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AttendanceCheckOutTest extends TestCase
{
    use RefreshDatabase;

    private User $securityUser;
    private Employee $employee;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->securityUser = User::factory()->create(['role' => UserRole::Security]);
        $department = Department::create(['name' => 'Security GPA']);
        $shift = Shift::create([
            'name' => 'Shift 1',
            'check_in_time' => '07:00:00',
            'tolerance_time' => '07:15:00',
            'check_out_time' => '14:00:00',
            'spans_next_day' => false,
        ]);

        $this->employee = Employee::create([
            'name' => 'Timot',
            'department_id' => $department->id,
            'shift_id' => $shift->id,
            'is_active' => true,
        ]);
    }

    public function test_check_out_rejected_before_seven_hours(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-29 13:00:00', 'Asia/Jakarta'));

        Attendance::create([
            'employee_id' => $this->employee->id,
            'attendance_date' => '2026-06-29',
            'check_in_at' => Carbon::parse('2026-06-29 07:00:00', 'Asia/Jakarta'),
            'status' => AttendanceStatus::Present,
            'recorded_by' => $this->securityUser->id,
        ]);

        $response = $this->actingAs($this->securityUser)->postJson(route('security.attendance.check-out'), [
            'employee_id' => $this->employee->id,
            'latitude' => -6.242792163317656,
            'longitude' => 106.84609367942863,
            'photo' => UploadedFile::fake()->image('checkout.jpg'),
        ]);

        $response->assertStatus(422);
        $this->assertStringContainsString('minimal 7 jam', $response->json('message'));
    }

    public function test_check_out_success_after_seven_hours(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-29 14:05:00', 'Asia/Jakarta'));

        Attendance::create([
            'employee_id' => $this->employee->id,
            'attendance_date' => '2026-06-29',
            'check_in_at' => Carbon::parse('2026-06-29 07:00:00', 'Asia/Jakarta'),
            'status' => AttendanceStatus::Present,
            'recorded_by' => $this->securityUser->id,
        ]);

        $response = $this->actingAs($this->securityUser)->postJson(route('security.attendance.check-out'), [
            'employee_id' => $this->employee->id,
            'latitude' => -6.242792163317656,
            'longitude' => 106.84609367942863,
            'photo' => UploadedFile::fake()->image('checkout.jpg'),
        ]);

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertNotNull(Attendance::first()->check_out_at);
    }
}
