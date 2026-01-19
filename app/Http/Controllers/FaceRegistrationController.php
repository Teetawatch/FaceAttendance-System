<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FaceRegistrationController extends Controller
{
    /**
     * แสดงหน้าลงทะเบียนใบหน้า
     */
    public function index()
    {
        $employees = Employee::where('is_active', true)
            ->orderBy('first_name')
            ->get(['id', 'employee_code', 'first_name', 'last_name', 'photo_path']);

        // Prepare data for JavaScript
        $employeeList = $employees->map(function ($e) {
            return [
                'id' => $e->id,
                'employee_code' => $e->employee_code,
                'name' => $e->first_name . ' ' . $e->last_name,
                'has_photo' => !empty($e->photo_path),
                'photo_url' => $e->photo_path ? route('storage.file', ['path' => $e->photo_path]) : null,
            ];
        });

        return view('monitor.face-register', compact('employees', 'employeeList'));
    }

    /**
     * API: ดึงรายชื่อพนักงาน
     */
    public function getEmployees()
    {
        $employees = Employee::where('is_active', true)
            ->orderBy('first_name')
            ->get(['id', 'employee_code', 'first_name', 'last_name', 'photo_path'])
            ->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'employee_code' => $employee->employee_code,
                    'name' => $employee->first_name . ' ' . $employee->last_name,
                    'has_photo' => !empty($employee->photo_path),
                    'photo_url' => $employee->photo_path 
                        ? route('storage.file', ['path' => $employee->photo_path]) 
                        : null,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $employees
        ]);
    }

    /**
     * บันทึกรูปใบหน้าจากกล้อง
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'photo' => 'required|string', // Base64 encoded image
        ], [
            'employee_id.required' => 'กรุณาเลือกพนักงาน',
            'employee_id.exists' => 'ไม่พบพนักงานในระบบ',
            'photo.required' => 'กรุณาถ่ายรูปก่อนบันทึก',
        ]);

        try {
            $employee = Employee::findOrFail($request->employee_id);

            // Decode Base64 image
            $imageData = $request->photo;
            
            // Remove data:image/jpeg;base64, prefix if present
            if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $matches)) {
                $extension = $matches[1];
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
            } else {
                $extension = 'jpg';
            }

            $imageData = base64_decode($imageData);

            if ($imageData === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'รูปภาพไม่ถูกต้อง'
                ], 400);
            }

            // Generate unique filename
            $filename = 'employees/' . $employee->employee_code . '_' . Str::random(8) . '.' . $extension;

            // Delete old photo if exists
            if ($employee->photo_path) {
                Storage::disk('public')->delete($employee->photo_path);
            }

            // Save new photo
            Storage::disk('public')->put($filename, $imageData);

            // Update employee record
            $employee->update(['photo_path' => $filename]);

            return response()->json([
                'success' => true,
                'message' => 'บันทึกใบหน้าสำเร็จ!',
                'data' => [
                    'employee_code' => $employee->employee_code,
                    'name' => $employee->first_name . ' ' . $employee->last_name,
                    'photo_url' => route('storage.file', ['path' => $filename]),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], 500);
        }
    }
}
