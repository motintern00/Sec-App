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

class AttendanceCheckInTest extends TestCase
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

    public function test_check_in_success_within_radius(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-29 07:05:00', 'Asia/Jakarta'));

        $response = $this->actingAs($this->securityUser)->postJson(route('security.attendance.check-in'), [
            'employee_id' => $this->employee->id,
            'latitude' => -6.242792163317656,
            'longitude' => 106.84609367942863,
            'photo' => UploadedFile::fake()->image('checkin.jpg'),
        ]);

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertDatabaseHas('attendances', [
            'employee_id' => $this->employee->id,
            'status' => AttendanceStatus::Late->value,
        ]);
    }

    public function test_check_in_rejected_outside_radius(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-29 07:05:00', 'Asia/Jakarta'));

        $response = $this->actingAs($this->securityUser)->postJson(route('security.attendance.check-in'), [
            'employee_id' => $this->employee->id,
            'latitude' => -6.250000,
            'longitude' => 106.900000,
            'photo' => UploadedFile::fake()->image('checkin.jpg'),
        ]);

        $response->assertStatus(422)->assertJson([
            'success' => false,
            'message' => 'Anda berada di luar area kantor. Absensi ditolak.',
        ]);
    }

    public function test_check_in_rejected_when_already_checked_in(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-29 07:05:00', 'Asia/Jakarta'));

        Attendance::create([
            'employee_id' => $this->employee->id,
            'attendance_date' => '2026-06-29',
            'check_in_at' => now(),
            'status' => AttendanceStatus::Present,
            'recorded_by' => $this->securityUser->id,
        ]);

        $response = $this->actingAs($this->securityUser)->postJson(route('security.attendance.check-in'), [
            'employee_id' => $this->employee->id,
            'latitude' => -6.242792163317656,
            'longitude' => 106.84609367942863,
            'photo' => UploadedFile::fake()->image('checkin.jpg'),
        ]);

        $response->assertStatus(422);
    }
}
