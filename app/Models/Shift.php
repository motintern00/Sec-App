<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shift extends Model
{
    protected $fillable = [
        'name',
        'check_in_time',
        'tolerance_time',
        'check_out_time',
        'spans_next_day',
    ];

    protected function casts(): array
    {
        return [
            'spans_next_day' => 'boolean',
        ];
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
