<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Department;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeCrudTest extends TestCase
{
    use RefreshDatabase;

  public function test_admin_can_create_employee(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $department = Department::create(['name' => 'Security GPA']);
        $shift = Shift::create([
            'name' => 'Shift 1',
            'check_in_time' => '07:00:00',
            'tolerance_time' => '07:15:00',
            'check_out_time' => '14:00:00',
            'spans_next_day' => false,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.employees.store'), [
            'name' => 'Timot',
            'department_id' => $department->id,
            'shift_id' => $shift->id,
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('admin.employees.index'));
        $this->assertDatabaseHas('employees', ['name' => 'Timot']);
    }

    public function test_security_cannot_access_employee_crud(): void
    {
        $security = User::factory()->create(['role' => UserRole::Security]);

        $this->actingAs($security)->get(route('admin.employees.index'))->assertForbidden();
    }
}
