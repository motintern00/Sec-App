<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class CheckInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSecurity() ?? false;
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'exists:employees,id'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'photo' => ['required', 'image', 'max:5120'],
        ];
    }

    public function attributes(): array
    {
        return [
            'employee_id' => 'pegawai',
            'latitude' => 'latitude',
            'longitude' => 'longitude',
            'photo' => 'foto',
        ];
    }
}
