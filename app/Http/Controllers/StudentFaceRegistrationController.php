<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StudentFaceRegistrationController extends Controller
{
    /**
     * แสดงหน้าลงทะเบียนใบหน้านักเรียน
     */
    public function index(Request $request)
    {
        $courses = Course::orderBy('name')->get();
        $selectedCourseId = $request->query('course_id');

        $studentsQuery = Student::where('is_active', true)
            ->orderBy('first_name');

        if ($selectedCourseId) {
            $studentsQuery->where('course_id', $selectedCourseId);
        }

        $students = $studentsQuery->with('course')->get(['id', 'student_code', 'first_name', 'last_name', 'course_id', 'photo_path']);

        // Prepare data for JavaScript
        $studentList = $students->map(function ($s) {
            return [
                'id' => $s->id,
                'student_code' => $s->student_code,
                'name' => $s->first_name . ' ' . $s->last_name,
                'course_name' => $s->course?->name ?? 'ไม่ระบุหลักสูตร',
                'has_photo' => !empty($s->photo_path),
                'photo_url' => $s->photo_path ? route('storage.file', ['path' => $s->photo_path]) : null,
            ];
        });

        return view('students.face-register', compact('courses', 'students', 'studentList', 'selectedCourseId'));
    }

    /**
     * API: ดึงรายชื่อนักเรียน (พร้อม filter ตาม course)
     */
    public function getStudents(Request $request)
    {
        $query = Student::where('is_active', true)
            ->orderBy('first_name');

        if ($request->has('course_id') && $request->course_id) {
            $query->where('course_id', $request->course_id);
        }

        $students = $query->with('course')
            ->get(['id', 'student_code', 'first_name', 'last_name', 'course_id', 'photo_path'])
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'student_code' => $student->student_code,
                    'name' => $student->first_name . ' ' . $student->last_name,
                    'course_name' => $student->course?->name ?? 'ไม่ระบุหลักสูตร',
                    'has_photo' => !empty($student->photo_path),
                    'photo_url' => $student->photo_path 
                        ? route('storage.file', ['path' => $student->photo_path]) 
                        : null,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $students
        ]);
    }

    /**
     * บันทึกรูปใบหน้าจากกล้อง
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'photo' => 'required|string', // Base64 encoded image
        ], [
            'student_id.required' => 'กรุณาเลือกนักเรียน',
            'student_id.exists' => 'ไม่พบนักเรียนในระบบ',
            'photo.required' => 'กรุณาถ่ายรูปก่อนบันทึก',
        ]);

        try {
            $student = Student::findOrFail($request->student_id);

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
            $filename = 'students/' . $student->student_code . '_' . Str::random(8) . '.' . $extension;

            // Delete old photo if exists
            if ($student->photo_path) {
                Storage::disk('public')->delete($student->photo_path);
            }

            // Save new photo
            Storage::disk('public')->put($filename, $imageData);

            // Update student record
            $student->update(['photo_path' => $filename]);

            return response()->json([
                'success' => true,
                'message' => 'บันทึกใบหน้าสำเร็จ!',
                'data' => [
                    'student_code' => $student->student_code,
                    'name' => $student->first_name . ' ' . $student->last_name,
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
