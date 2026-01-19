<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentAttendanceLog;
use App\Models\Course;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StudentReportController extends Controller
{
    public function index(Request $request)
    {
        // Get filter parameters
        $courseId = $request->input('course_id');
        $startDate = $request->input('start_date', now()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        // Get courses for filter dropdown
        $courses = Course::orderBy('created_at', 'desc')->get();

        // Build query
        $query = StudentAttendanceLog::with(['student.course', 'device'])
            ->whereBetween('scan_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($courseId) {
            $query->whereHas('student', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        }

        $logs = $query->orderBy('scan_time', 'desc')->paginate(20);

        // Get summary statistics
        $totalScans = StudentAttendanceLog::whereBetween('scan_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        if ($courseId) {
            $totalScans->whereHas('student', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        }
        $totalScansCount = $totalScans->count();

        // Unique students scanned
        $uniqueStudents = StudentAttendanceLog::whereBetween('scan_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        if ($courseId) {
            $uniqueStudents->whereHas('student', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        }
        $uniqueStudentsCount = $uniqueStudents->distinct('student_id')->count('student_id');

        // Total students in course
        $totalStudentsQuery = Student::where('is_active', true);
        if ($courseId) {
            $totalStudentsQuery->where('course_id', $courseId);
        }
        $totalStudents = $totalStudentsQuery->count();

        // Get all students in the selected course(s)
        $allStudentsQuery = Student::where('is_active', true);
        if ($courseId) {
            $allStudentsQuery->where('course_id', $courseId);
        }
        $allStudents = $allStudentsQuery->with('course')->get();

        // Get student IDs who have scanned (check-in) in the date range
        $scannedStudentIds = StudentAttendanceLog::whereBetween('scan_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('scan_type', 'in');
        if ($courseId) {
            $scannedStudentIds->whereHas('student', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        }
        $scannedStudentIds = $scannedStudentIds->pluck('student_id')->unique();

        // Absent students (haven't checked in at all)
        $absentStudents = $allStudents->filter(function ($student) use ($scannedStudentIds) {
            return !$scannedStudentIds->contains($student->id);
        });

        // Late students (scanned with is_late = true)
        $lateStudentsQuery = StudentAttendanceLog::with('student.course')
            ->whereBetween('scan_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('scan_type', 'in')
            ->where('is_late', true);
        if ($courseId) {
            $lateStudentsQuery->whereHas('student', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        }
        $lateStudents = $lateStudentsQuery->orderBy('scan_time', 'desc')->get();

        // Count statistics
        $absentCount = $absentStudents->count();
        $lateCount = $lateStudents->unique('student_id')->count();

        return view('student-reports.index', compact(
            'logs',
            'courses',
            'courseId',
            'startDate',
            'endDate',
            'totalScansCount',
            'uniqueStudentsCount',
            'totalStudents',
            'absentStudents',
            'lateStudents',
            'absentCount',
            'lateCount'
        ));
    }

    public function export(Request $request)
    {
        $courseId = $request->input('course_id');
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        // Build query
        $query = StudentAttendanceLog::with(['student.course', 'device'])
            ->whereBetween('scan_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($courseId) {
            $query->whereHas('student', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        }

        $logs = $query->orderBy('scan_time', 'asc')->get();

        // Generate CSV
        $headers = ['รหัสนักเรียน', 'ชื่อ-นามสกุล', 'หลักสูตร', 'วันที่', 'เวลา', 'ประเภท'];
        
        $callback = function() use ($headers, $logs) {
            $file = fopen('php://output', 'w');
            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $headers);
            
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->student->student_code ?? '-',
                    ($log->student->first_name ?? '') . ' ' . ($log->student->last_name ?? ''),
                    $log->student->course->name ?? '-',
                    $log->scan_time->format('d/m/Y'),
                    $log->scan_time->format('H:i:s'),
                    $log->scan_type === 'in' ? 'เข้าเรียน' : 'ออก',
                ]);
            }
            fclose($file);
        };

        $filename = 'student_attendance_' . $startDate . '_to_' . $endDate . '.csv';

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Export PDF report
     * 
     * Two check-in periods:
     * - Morning: 05:30 - 08:00 (late after 08:00)
     * - Afternoon: 12:30 - 13:00 (late after 13:00)
     */
    public function exportPdf(Request $request)
    {
        // Get filter parameters
        $courseId = $request->input('course_id');
        $date = $request->input('date', now()->format('Y-m-d'));

        // Get all students in the selected course(s)
        $allStudentsQuery = Student::where('is_active', true);
        if ($courseId) {
            $allStudentsQuery->where('course_id', $courseId);
        }
        $allStudents = $allStudentsQuery->with('course')->get();

        // Build query - get all logs for the selected date
        $query = StudentAttendanceLog::with(['student.course'])
            ->whereDate('scan_time', $date);

        if ($courseId) {
            $query->whereHas('student', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        }

        $logs = $query->orderBy('student_id')->orderBy('scan_time')->get();

        // Get student IDs who have scanned on this date
        $scannedStudentIds = $logs->pluck('student_id')->unique();

        // Group scanned students by student_id with morning/afternoon periods
        $scannedStudentLogs = $logs->groupBy('student_id')->map(function ($studentLogs) {
            $morning = $studentLogs->where('period', 'morning')->first();
            $afternoon = $studentLogs->where('period', 'afternoon')->first();
            
            // Determine status based on scans
            $status = 'ปกติ';
            $morningLate = $morning && $morning->is_late;
            $afternoonLate = $afternoon && $afternoon->is_late;
            
            if ($morningLate || $afternoonLate) {
                $status = 'มาสาย';
            }
            if (!$morning && !$afternoon) {
                $status = 'ไม่มาลงชื่อ';
            }
            
            return [
                'student' => $studentLogs->first()->student,
                'morning' => $morning,
                'afternoon' => $afternoon,
                'morning_late' => $morningLate,
                'afternoon_late' => $afternoonLate,
                'status' => $status,
            ];
        })->values();

        // Add absent students (those who haven't scanned at all)
        $absentStudentLogs = $allStudents->filter(function ($student) use ($scannedStudentIds) {
            return !$scannedStudentIds->contains($student->id);
        })->map(function ($student) {
            return [
                'student' => $student,
                'morning' => null,
                'afternoon' => null,
                'morning_late' => false,
                'afternoon_late' => false,
                'status' => 'ไม่มาลงชื่อ',
            ];
        })->values();

        // Merge scanned students and absent students
        $studentLogs = $scannedStudentLogs->concat($absentStudentLogs);

        // Calculate totals
        $totalStudents = $allStudents->count();
        $presentCount = $studentLogs->where('status', 'ปกติ')->count();
        $lateCount = $studentLogs->where('status', 'มาสาย')->count();
        $absentCount = $studentLogs->where('status', 'ไม่มาลงชื่อ')->count();

        $courseName = $courseId ? Course::find($courseId)?->name : 'ทุกหลักสูตร';
        $courses = Course::orderBy('created_at', 'desc')->get();

        return view('student-reports.pdf', compact(
            'studentLogs', 
            'courseName', 
            'date', 
            'courses', 
            'courseId',
            'totalStudents',
            'presentCount',
            'lateCount',
            'absentCount'
        ));
    }

    /**
     * Send report to email
     */
    public function sendEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'กรุณากรอกอีเมล',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
        ]);

        $courseId = $request->input('course_id');
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $email = $request->input('email');

        // Build query
        $query = StudentAttendanceLog::with(['student.course', 'device'])
            ->whereBetween('scan_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($courseId) {
            $query->whereHas('student', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        }

        $logs = $query->orderBy('scan_time', 'asc')->get();

        // Get course name
        $courseName = $courseId ? Course::find($courseId)?->name : 'ทุกหลักสูตร';

        // Generate CSV content
        $csvContent = $this->generateCsvContent($logs);

        // Create temp file
        $filename = 'student_attendance_' . $startDate . '_to_' . $endDate . '.csv';
        $tempPath = storage_path('app/temp/' . $filename);
        
        // Ensure temp directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        
        file_put_contents($tempPath, $csvContent);

        try {
            // Send email with attachment
            \Illuminate\Support\Facades\Mail::raw(
                "รายงานการเข้าเรียนนักเรียน\n\n" .
                "หลักสูตร: {$courseName}\n" .
                "ช่วงวันที่: {$startDate} ถึง {$endDate}\n" .
                "จำนวนรายการ: " . $logs->count() . " รายการ\n\n" .
                "ไฟล์รายงานแนบมาพร้อมอีเมลนี้",
                function ($message) use ($email, $tempPath, $filename, $courseName, $startDate, $endDate) {
                    $message->to($email)
                            ->subject("รายงานการเข้าเรียน - {$courseName} ({$startDate} ถึง {$endDate})")
                            ->attach($tempPath, [
                                'as' => $filename,
                                'mime' => 'text/csv',
                            ]);
                }
            );

            // Delete temp file
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }

            return redirect()->route('student-reports.index', $request->query())
                ->with('success', "ส่งรายงานไปที่ {$email} เรียบร้อยแล้ว");

        } catch (\Exception $e) {
            // Delete temp file on error
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }

            return redirect()->route('student-reports.index', $request->query())
                ->with('error', 'ไม่สามารถส่งอีเมลได้: ' . $e->getMessage());
        }
    }

    /**
     * Generate CSV content string
     */
    private function generateCsvContent($logs)
    {
        $output = chr(0xEF) . chr(0xBB) . chr(0xBF); // UTF-8 BOM
        
        // Headers
        $headers = ['รหัสนักเรียน', 'ชื่อ-นามสกุล', 'หลักสูตร', 'วันที่', 'เวลา', 'ประเภท'];
        $output .= implode(',', $headers) . "\n";
        
        // Data rows
        foreach ($logs as $log) {
            $row = [
                $log->student->student_code ?? '-',
                '"' . ($log->student->first_name ?? '') . ' ' . ($log->student->last_name ?? '') . '"',
                '"' . ($log->student->course->name ?? '-') . '"',
                $log->scan_time->format('d/m/Y'),
                $log->scan_time->format('H:i:s'),
                $log->scan_type === 'in' ? 'เข้าเรียน' : 'ออก',
            ];
            $output .= implode(',', $row) . "\n";
        }
        
        return $output;
    }
}
