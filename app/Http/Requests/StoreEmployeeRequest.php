<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // อนุญาตให้ทุกคนที่ผ่าน Middleware เข้ามาใช้ได้
    }

    public function rules(): array
    {
        return [
            'employee_code' => 'required|string|unique:employees,employee_code|max:50',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'department' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'photo' => 'nullable|image|max:2048', // รองรับไฟล์รูปภาพไม่เกิน 2MB
        ];
    }
}