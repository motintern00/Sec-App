<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_redirects_to_admin_dashboard(): void
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

    public function test_security_redirects_to_attendance_page(): void
    {
        $security = User::factory()->create([
            'email' => 'security@test.local',
            'role' => UserRole::Security,
        ]);

        $response = $this->post('/login', [
            'email' => $security->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
    }
}
