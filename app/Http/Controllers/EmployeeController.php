<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Imports\EmployeeImport;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller
{
    /**
     * Show import form/modal
     */
    public function showImportForm()
    {
        return view('employees.import');
    }

    /**
     * Process Excel import
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ], [
            'file.required' => 'กรุณาเลือกไฟล์ Excel',
            'file.mimes' => 'ไฟล์ต้องเป็น .xlsx, .xls หรือ .csv เท่านั้น',
            'file.max' => 'ขนาดไฟล์ต้องไม่เกิน 2MB',
        ]);

        try {
            $import = new EmployeeImport();
            Excel::import($import, $request->file('file'));

            $importedCount = $import->getImportedCount();
            $updatedCount = $import->getUpdatedCount();
            $skippedRows = $import->getSkippedRows();

            // Check if any data was imported
            if ($importedCount == 0 && $updatedCount == 0) {
                return redirect()->back()
                    ->with('error', 'ไม่พบข้อมูลที่สามารถนำเข้าได้ กรุณาตรวจสอบรูปแบบไฟล์และ Header (รหัสพนักงาน, ชื่อจริง, นามสกุล, แผนก, ตำแหน่ง)');
            }

            $message = "นำเข้าข้อมูลสำเร็จ!";
            if ($importedCount > 0) {
                $message .= " เพิ่มใหม่ {$importedCount} คน";
            }
            if ($updatedCount > 0) {
                $message .= " อัพเดต {$updatedCount} คน";
            }

            // If there are skipped rows, show warning
            if (!empty($skippedRows)) {
                return redirect()->route('employees.index')
                    ->with('success', $message)
                    ->with('import_warnings', $skippedRows);
            }

            return redirect()->route('employees.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    /**
     * Download Excel template
     */
    public function downloadTemplate()
    {
        $headers = ['รหัสพนักงาน', 'ชื่อจริง', 'นามสกุล', 'แผนก', 'ตำแหน่ง'];
        $exampleData = [
            ['EMP001', 'สมชาย', 'ใจดี', 'ฝ่ายบุคคล', 'เจ้าหน้าที่'],
            ['EMP002', 'สมหญิง', 'รักงาน', 'ฝ่ายการเงิน', 'นักวิชาการ'],
        ];

        $callback = function() use ($headers, $exampleData) {
            $file = fopen('php://output', 'w');
            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $headers);
            foreach ($exampleData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->streamDownload($callback, 'employee_template.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function index()
    {
        // ดึงข้อมูลพร้อม Pagination (10 คนต่อหน้า)
        $employees = Employee::latest()->paginate(10);
        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(StoreEmployeeRequest $request)
    {
        $data = $request->validated();

        // จัดการอัปโหลดรูปภาพ
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('employees', 'public');
            $data['photo_path'] = $path;
        }

        Employee::create($data);

        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            // ลบรูปเก่าถ้ามี
            if ($employee->photo_path) {
                Storage::disk('public')->delete($employee->photo_path);
            }
            $path = $request->file('photo')->store('employees', 'public');
            $data['photo_path'] = $path;
        }

        $employee->update($data);

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        if ($employee->photo_path) {
            Storage::disk('public')->delete($employee->photo_path);
        }
        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }
}