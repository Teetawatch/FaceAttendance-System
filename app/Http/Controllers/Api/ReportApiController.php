<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\StudentAttendanceLog;
use App\Models\Employee;
use App\Models\Student;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportApiController extends Controller
{
    /**
     * ดึงข้อมูลการเข้า-ออกงานของข้าราชการ (Real-time)
     * 
     * GET /api/v1/reports/staff-attendance
     * 
     * Parameters:
     * - date: วันที่ต้องการ (format: Y-m-d) default: วันนี้
     * - department: กรองตามแผนก (optional)
     * - employee_id: กรองตาม employee_id (optional)
     * - limit: จำนวนรายการ (default: 100)
     */
    public function staffAttendance(Request $request)
    {
        $date = $request->get('date', Carbon::today()->toDateString());
        $department = $request->get('department');
        $employeeId = $request->get('employee_id');
        $limit = min($request->get('limit', 100), 500);

        $query = AttendanceLog::with(['employee:id,employee_code,first_name,last_name,department,position', 'device:id,name'])
            ->whereDate('scan_time', $date)
            ->orderBy('scan_time', 'desc');

        // กรองตามแผนก
        if ($department) {
            $query->whereHas('employee', function ($q) use ($department) {
                $q->where('department', 'like', "%{$department}%");
            });
        }

        // กรองตาม employee
        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $logs = $query->limit($limit)->get();

        // จัดรูปแบบข้อมูล
        $data = $logs->map(function ($log) use ($request) {
            return [
                'id' => $log->id,
                'employee_code' => $log->employee->employee_code ?? null,
                'full_name' => $log->employee ? $log->employee->first_name . ' ' . $log->employee->last_name : null,
                'department' => $log->employee->department ?? null,
                'position' => $log->employee->position ?? null,
                'scan_type' => $log->scan_type, // 'in' หรือ 'out'
                'scan_time' => $log->scan_time->format('Y-m-d H:i:s'),
                'is_late' => $log->is_late,
                'device_name' => $log->device->name ?? null,
                'snapshot_url' => $log->snapshot_path 
                    ? url('storage/' . $log->snapshot_path) 
                    : null,
            ];
        });

        return response()->json([
            'success' => true,
            'date' => $date,
            'total_records' => $logs->count(),
            'data' => $data,
            'fetched_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * ดึงข้อมูลการเข้าเรียนของนักเรียน (Real-time)
     * 
     * GET /api/v1/reports/student-attendance
     * 
     * Parameters:
     * - date: วันที่ต้องการ (format: Y-m-d) default: วันนี้
     * - course_id: กรองตามหลักสูตร (optional)
     * - student_id: กรองตาม student_id (optional)
     * - period: กรองตามช่วง 'morning' หรือ 'afternoon' (optional)
     * - limit: จำนวนรายการ (default: 100)
     */
    public function studentAttendance(Request $request)
    {
        $date = $request->get('date', Carbon::today()->toDateString());
        $courseId = $request->get('course_id');
        $studentId = $request->get('student_id');
        $period = $request->get('period');
        $limit = min($request->get('limit', 100), 500);

        $query = StudentAttendanceLog::with(['student:id,student_code,first_name,last_name,course_id', 'student.course:id,name', 'device:id,name'])
            ->whereDate('scan_time', $date)
            ->orderBy('scan_time', 'desc');

        // กรองตามหลักสูตร
        if ($courseId) {
            $query->whereHas('student', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        }

        // กรองตาม student
        if ($studentId) {
            $query->where('student_id', $studentId);
        }

        // กรองตามช่วงเวลา
        if ($period) {
            $query->where('period', $period);
        }

        $logs = $query->limit($limit)->get();

        // จัดรูปแบบข้อมูล
        $data = $logs->map(function ($log) use ($request) {
            return [
                'id' => $log->id,
                'student_code' => $log->student->student_code ?? null,
                'full_name' => $log->student ? $log->student->first_name . ' ' . $log->student->last_name : null,
                'course_name' => $log->student->course->name ?? null,
                'scan_type' => $log->scan_type, // 'in' หรือ 'out'
                'period' => $log->period, // 'morning' หรือ 'afternoon'
                'scan_time' => $log->scan_time->format('Y-m-d H:i:s'),
                'is_late' => $log->is_late,
                'device_name' => $log->device->name ?? null,
                'snapshot_url' => $log->snapshot_path 
                    ? url('storage/' . $log->snapshot_path) 
                    : null,
            ];
        });

        return response()->json([
            'success' => true,
            'date' => $date,
            'total_records' => $logs->count(),
            'data' => $data,
            'fetched_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * ดึงสรุปการเข้างานข้าราชการวันนี้ (Dashboard)
     * 
     * GET /api/v1/reports/staff-summary
     */
    public function staffSummary(Request $request)
    {
        $date = $request->get('date', Carbon::today()->toDateString());

        $totalEmployees = Employee::where('is_active', true)->count();
        
        $checkedIn = AttendanceLog::whereDate('scan_time', $date)
            ->where('scan_type', 'in')
            ->distinct('employee_id')
            ->count('employee_id');

        $late = AttendanceLog::whereDate('scan_time', $date)
            ->where('scan_type', 'in')
            ->where('is_late', true)
            ->distinct('employee_id')
            ->count('employee_id');

        $onTime = $checkedIn - $late;
        $absent = $totalEmployees - $checkedIn;

        // รายการสแกนล่าสุด 10 รายการ
        $recentScans = AttendanceLog::with('employee:id,first_name,last_name,department')
            ->whereDate('scan_time', $date)
            ->orderBy('scan_time', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($log) {
                return [
                    'full_name' => $log->employee ? $log->employee->first_name . ' ' . $log->employee->last_name : null,
                    'department' => $log->employee->department ?? null,
                    'scan_type' => $log->scan_type,
                    'scan_time' => $log->scan_time->format('H:i:s'),
                    'is_late' => $log->is_late,
                ];
            });

        return response()->json([
            'success' => true,
            'date' => $date,
            'summary' => [
                'total_employees' => $totalEmployees,
                'checked_in' => $checkedIn,
                'on_time' => $onTime,
                'late' => $late,
                'absent' => $absent,
                'attendance_rate' => $totalEmployees > 0 
                    ? round(($checkedIn / $totalEmployees) * 100, 1) 
                    : 0,
            ],
            'recent_scans' => $recentScans,
            'fetched_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * ดึงสรุปการเข้าเรียนนักเรียนวันนี้ (Dashboard)
     * 
     * GET /api/v1/reports/student-summary
     */
    public function studentSummary(Request $request)
    {
        $date = $request->get('date', Carbon::today()->toDateString());
        $courseId = $request->get('course_id');

        $query = Student::where('is_active', true);
        if ($courseId) {
            $query->where('course_id', $courseId);
        }
        $totalStudents = $query->count();

        $logQuery = StudentAttendanceLog::whereDate('scan_time', $date)
            ->where('scan_type', 'in');
        
        if ($courseId) {
            $logQuery->whereHas('student', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        }

        $checkedIn = (clone $logQuery)->distinct('student_id')->count('student_id');
        $late = (clone $logQuery)->where('is_late', true)->distinct('student_id')->count('student_id');
        $onTime = $checkedIn - $late;
        $absent = $totalStudents - $checkedIn;

        // รายการสแกนล่าสุด 10 รายการ
        $recentQuery = StudentAttendanceLog::with('student:id,first_name,last_name,course_id', 'student.course:id,name')
            ->whereDate('scan_time', $date)
            ->orderBy('scan_time', 'desc');
        
        if ($courseId) {
            $recentQuery->whereHas('student', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        }

        $recentScans = $recentQuery->limit(10)->get()->map(function ($log) {
            return [
                'full_name' => $log->student ? $log->student->first_name . ' ' . $log->student->last_name : null,
                'course_name' => $log->student->course->name ?? null,
                'scan_type' => $log->scan_type,
                'period' => $log->period,
                'scan_time' => $log->scan_time->format('H:i:s'),
                'is_late' => $log->is_late,
            ];
        });

        return response()->json([
            'success' => true,
            'date' => $date,
            'course_id' => $courseId,
            'summary' => [
                'total_students' => $totalStudents,
                'checked_in' => $checkedIn,
                'on_time' => $onTime,
                'late' => $late,
                'absent' => $absent,
                'attendance_rate' => $totalStudents > 0 
                    ? round(($checkedIn / $totalStudents) * 100, 1) 
                    : 0,
            ],
            'recent_scans' => $recentScans,
            'fetched_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * รายชื่อข้าราชการทั้งหมด
     * 
     * GET /api/v1/reports/employees
     */
    public function employees(Request $request)
    {
        $department = $request->get('department');
        $isActive = $request->get('is_active', true);

        $query = Employee::select('id', 'employee_code', 'first_name', 'last_name', 'department', 'position', 'is_active');
        
        if ($department) {
            $query->where('department', 'like', "%{$department}%");
        }
        
        if ($isActive !== null) {
            $query->where('is_active', $isActive);
        }

        $employees = $query->orderBy('first_name')->get();

        return response()->json([
            'success' => true,
            'total' => $employees->count(),
            'data' => $employees,
        ]);
    }

    /**
     * รายชื่อนักเรียนทั้งหมด
     * 
     * GET /api/v1/reports/students
     */
    public function students(Request $request)
    {
        $courseId = $request->get('course_id');
        $isActive = $request->get('is_active', true);

        $query = Student::with('course:id,name')
            ->select('id', 'student_code', 'first_name', 'last_name', 'course_id', 'is_active');
        
        if ($courseId) {
            $query->where('course_id', $courseId);
        }
        
        if ($isActive !== null) {
            $query->where('is_active', $isActive);
        }

        $students = $query->orderBy('first_name')->get()->map(function ($student) {
            return [
                'id' => $student->id,
                'student_code' => $student->student_code,
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'full_name' => $student->first_name . ' ' . $student->last_name,
                'course_id' => $student->course_id,
                'course_name' => $student->course->name ?? null,
                'is_active' => $student->is_active,
            ];
        });

        return response()->json([
            'success' => true,
            'total' => $students->count(),
            'data' => $students,
        ]);
    }
}
