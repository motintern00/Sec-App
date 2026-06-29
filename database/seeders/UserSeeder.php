<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@gpa.local'],
            [
                'name' => 'Admin GPA',
                'password' => Hash::make('password'),
                'role' => UserRole::Admin,
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'security@gpa.local'],
            [
                'name' => 'Security GPA',
                'password' => Hash::make('password'),
                'role' => UserRole::Security,
            ]
        );
    }
}
