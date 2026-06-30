<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Employee;
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
                'employee_id' => null,
                'phone' => null,
            ]
        );

        User::query()->where('email', 'security@gpa.local')->delete();

        Employee::query()->whereNotNull('email')->get()->each(function (Employee $employee) {
            User::query()->updateOrCreate(
                ['email' => $employee->email],
                [
                    'name' => $employee->name,
                    'password' => Hash::make('password'),
                    'role' => UserRole::Employee,
                    'employee_id' => $employee->id,
                    'phone' => $employee->phone,
                ]
            );
        });
    }
}
