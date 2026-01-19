<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::withCount('students')->latest()->paginate(10);
        return view('courses.index', compact('courses'));
    }

    public function create()
    {
        return view('courses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'กรุณากรอกชื่อหลักสูตร',
            'start_date.required' => 'กรุณาระบุวันเริ่มหลักสูตร',
            'end_date.required' => 'กรุณาระบุวันสิ้นสุดหลักสูตร',
            'end_date.after_or_equal' => 'วันสิ้นสุดต้องไม่ก่อนวันเริ่ม',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Course::create($validated);

        return redirect()->route('courses.index')->with('success', 'สร้างหลักสูตรเรียบร้อยแล้ว');
    }

    public function edit(Course $course)
    {
        return view('courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'กรุณากรอกชื่อหลักสูตร',
            'start_date.required' => 'กรุณาระบุวันเริ่มหลักสูตร',
            'end_date.required' => 'กรุณาระบุวันสิ้นสุดหลักสูตร',
            'end_date.after_or_equal' => 'วันสิ้นสุดต้องไม่ก่อนวันเริ่ม',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $course->update($validated);

        return redirect()->route('courses.index')->with('success', 'อัพเดตหลักสูตรเรียบร้อยแล้ว');
    }

    public function destroy(Course $course)
    {
        // Check if course has students
        if ($course->students()->count() > 0) {
            return redirect()->route('courses.index')
                ->with('error', 'ไม่สามารถลบหลักสูตรที่มีนักเรียนอยู่ได้');
        }

        $course->delete();

        return redirect()->route('courses.index')->with('success', 'ลบหลักสูตรเรียบร้อยแล้ว');
    }
}
