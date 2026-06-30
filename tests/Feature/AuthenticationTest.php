<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_redirects_to_dashboard(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@test.local',
            'role' => UserRole::Admin,
        ]);

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
    }

    public function test_employee_redirects_to_dashboard(): void
    {
        $employee = User::factory()->create([
            'email' => 'employee@test.local',
            'role' => UserRole::Employee,
        ]);

        $response = $this->post('/login', [
            'email' => $employee->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
    }
}
