<?php

namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        $shifts = [
            [
                'name' => 'Shift 1',
                'check_in_time' => '07:00:00',
                'tolerance_time' => '07:15:00',
                'check_out_time' => '14:00:00',
                'spans_next_day' => false,
            ],
            [
                'name' => 'Shift 2',
                'check_in_time' => '14:00:00',
                'tolerance_time' => '14:15:00',
                'check_out_time' => '21:00:00',
                'spans_next_day' => false,
            ],
            [
                'name' => 'Shift 3',
                'check_in_time' => '21:00:00',
                'tolerance_time' => '21:15:00',
                'check_out_time' => '04:00:00',
                'spans_next_day' => true,
            ],
        ];

        foreach ($shifts as $shift) {
            Shift::query()->updateOrCreate(['name' => $shift['name']], $shift);
        }
    }
}
