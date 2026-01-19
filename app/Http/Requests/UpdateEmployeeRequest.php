<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_code' => [
                'required', 
                'string', 
                'max:50', 
                Rule::unique('employees')->ignore($this->employee), // ยกเว้น ID ตัวเอง
            ],
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'department' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'photo' => 'nullable|image|max:2048',
        ];
    }
}