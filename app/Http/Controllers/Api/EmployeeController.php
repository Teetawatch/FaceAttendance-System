<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function getFaces()
    {
        $employees = Employee::where('is_active', true)
                             ->whereNotNull('photo_path')
                             ->select('employee_code', 'first_name', 'last_name', 'photo_path')
                             ->get()
                             ->map(function ($employee) {
                                 return [
                                     'employee_code' => $employee->employee_code,
                                     'name' => $employee->first_name . ' ' . $employee->last_name,
                                     'photo_url' => route('storage.file', ['path' => $employee->photo_path]),
                                 ];
                             });

        return response()->json([
            'success' => true,
            'data' => $employees
        ]);
    }
}
