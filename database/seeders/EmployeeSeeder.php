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
            ['name' => 'Timot', 'shift_index' => 0],
            ['name' => 'Timoty', 'shift_index' => 1],
            ['name' => 'Timoti', 'shift_index' => 2],
            ['name' => 'Timothy', 'shift_index' => 0],
            ['name' => 'Timotot', 'shift_index' => 1],
        ];

        foreach ($employees as $employee) {
            Employee::query()->updateOrCreate(
                ['name' => $employee['name']],
                [
                    'department_id' => $department->id,
                    'shift_id' => $shifts[$employee['shift_index']]->id,
                    'is_active' => true,
                ]
            );
        }
    }
}
