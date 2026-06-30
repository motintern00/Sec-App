<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Shift;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $department = Department::query()->where('name', 'Security GPA')->first();
        $shifts = Shift::query()->orderBy('id')->get();

        if (! $department || $shifts->isEmpty()) {
            return;
        }

        $employees = [
            ['name' => 'Timot', 'email' => 'timot@gpa.local', 'phone' => '628111111001', 'shift_index' => 0],
            ['name' => 'Timoty', 'email' => 'timoty@gpa.local', 'phone' => '628111111002', 'shift_index' => 1],
            ['name' => 'Timoti', 'email' => 'timoti@gpa.local', 'phone' => '628111111003', 'shift_index' => 2],
            ['name' => 'Timothy', 'email' => 'timothy@gpa.local', 'phone' => '628111111004', 'shift_index' => 0],
            ['name' => 'Timotot', 'email' => 'timotot@gpa.local', 'phone' => '628111111005', 'shift_index' => 1],
            ['name' => 'Budi', 'email' => 'budi@gpa.local', 'phone' => '628111111006', 'shift_index' => 2],
            ['name' => 'Andi', 'email' => 'andi@gpa.local', 'phone' => '628111111007', 'shift_index' => 0],
            ['name' => 'Sari', 'email' => 'sari@gpa.local', 'phone' => '628111111008', 'shift_index' => 1],
            ['name' => 'Doni', 'email' => 'doni@gpa.local', 'phone' => '628111111009', 'shift_index' => 2],
            ['name' => 'Rudi', 'email' => 'rudi@gpa.local', 'phone' => '628111111010', 'shift_index' => 0],
        ];

        foreach ($employees as $employee) {
            Employee::query()->updateOrCreate(
                ['name' => $employee['name']],
                [
                    'email' => $employee['email'],
                    'phone' => $employee['phone'],
                    'department_id' => $department->id,
                    'shift_id' => $shifts[$employee['shift_index']]->id,
                    'is_active' => true,
                ]
            );
        }
    }
}
