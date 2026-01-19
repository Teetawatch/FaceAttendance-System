<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Imports\StudentImport;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with('course')->latest();

        // Filter by course
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('student_code', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        $students = $query->paginate(10);
        $courses = Course::where('is_active', true)->get();

        return view('students.index', compact('students', 'courses'));
    }

    /**
     * Process Excel import
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
            'course_id' => 'nullable|exists:courses,id',
        ], [
            'file.required' => 'กรุณาเลือกไฟล์ Excel',
            'file.mimes' => 'ไฟล์ต้องเป็น .xlsx, .xls หรือ .csv เท่านั้น',
            'file.max' => 'ขนาดไฟล์ต้องไม่เกิน 2MB',
        ]);

        try {
            $import = new StudentImport($request->course_id);
            Excel::import($import, $request->file('file'));

            $importedCount = $import->getImportedCount();
            $updatedCount = $import->getUpdatedCount();

            // Check if any data was imported
            if ($importedCount == 0 && $updatedCount == 0) {
                return redirect()->back()
                    ->with('error', 'ไม่พบข้อมูลที่สามารถนำเข้าได้ กรุณาตรวจสอบรูปแบบไฟล์ (รหัสนักเรียน, ชื่อ, นามสกุล, หลักสูตร)');
            }

            $message = "นำเข้าข้อมูลสำเร็จ!";
            if ($importedCount > 0) {
                $message .= " เพิ่มใหม่ {$importedCount} คน";
            }
            if ($updatedCount > 0) {
                $message .= " อัพเดต {$updatedCount} คน";
            }

            return redirect()->route('students.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    /**
     * Download Excel template
     */
    public function downloadTemplate()
    {
        $headers = ['รหัสนักเรียน', 'ชื่อ', 'นามสกุล', 'หลักสูตร'];
        $exampleData = [
            ['STD001', 'สมชาย', 'ใจดี', 'หลักสูตรพนักงานใหม่'],
            ['STD002', 'สมหญิง', 'รักเรียน', 'หลักสูตรพนักงานใหม่'],
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

        return response()->streamDownload($callback, 'student_template.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }


    public function create()
    {
        $courses = Course::where('is_active', true)->get();
        return view('students.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_code' => 'required|string|max:50|unique:students',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'course_id' => 'required|exists:courses,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'boolean',
        ], [
            'student_code.required' => 'กรุณากรอกรหัสนักเรียน',
            'student_code.unique' => 'รหัสนักเรียนนี้มีอยู่แล้ว',
            'first_name.required' => 'กรุณากรอกชื่อ',
            'last_name.required' => 'กรุณากรอกนามสกุล',
            'course_id.required' => 'กรุณาเลือกหลักสูตร',
            'photo.image' => 'ไฟล์ต้องเป็นรูปภาพ',
            'photo.max' => 'ขนาดรูปต้องไม่เกิน 2MB',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('students', 'public');
            $validated['photo_path'] = $path;
        }

        unset($validated['photo']);

        Student::create($validated);

        return redirect()->route('students.index')->with('success', 'เพิ่มนักเรียนเรียบร้อยแล้ว');
    }

    public function edit(Student $student)
    {
        $courses = Course::where('is_active', true)->get();
        return view('students.edit', compact('student', 'courses'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'student_code' => 'required|string|max:50|unique:students,student_code,' . $student->id,
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'course_id' => 'required|exists:courses,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'boolean',
        ], [
            'student_code.required' => 'กรุณากรอกรหัสนักเรียน',
            'student_code.unique' => 'รหัสนักเรียนนี้มีอยู่แล้ว',
            'first_name.required' => 'กรุณากรอกชื่อ',
            'last_name.required' => 'กรุณากรอกนามสกุล',
            'course_id.required' => 'กรุณาเลือกหลักสูตร',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($student->photo_path) {
                Storage::disk('public')->delete($student->photo_path);
            }
            $path = $request->file('photo')->store('students', 'public');
            $validated['photo_path'] = $path;
        }

        unset($validated['photo']);

        $student->update($validated);

        return redirect()->route('students.index')->with('success', 'อัพเดตข้อมูลนักเรียนเรียบร้อยแล้ว');
    }

    public function destroy(Student $student)
    {
        // Delete photo
        if ($student->photo_path) {
            Storage::disk('public')->delete($student->photo_path);
        }

        $student->delete();

        return redirect()->route('students.index')->with('success', 'ลบนักเรียนเรียบร้อยแล้ว');
    }
}
