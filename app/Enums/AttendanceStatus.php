<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case Present = 'present';
    case Late = 'late';
    case Absent = 'absent';

    public function label(): string
    {
        return match ($this) {
            self::Present => 'Hadir',
            self::Late => 'Terlambat',
            self::Absent => 'Tidak Hadir',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Present => 'bg-success',
            self::Late => 'bg-warning text-dark',
            self::Absent => 'bg-danger',
        };
    }
}
