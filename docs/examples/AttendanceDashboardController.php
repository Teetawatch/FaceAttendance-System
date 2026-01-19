<?php
/**
 * ตัวอย่าง Controller ในเว็บไซต์อื่น
 * สำหรับแสดงรายงานการเข้า-ออกงาน
 */

namespace App\Http\Controllers;

use App\Services\FaceAttendanceService;
use Illuminate\Http\Request;

class AttendanceDashboardController extends Controller
{
    protected $attendanceService;

    public function __construct(FaceAttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * หน้า Dashboard แสดงสรุปการเข้างาน
     */
    public function index()
    {
        // ดึงสรุปข้าราชการ
        $staffSummary = $this->attendanceService->getStaffSummary();
        
        // ดึงสรุปนักเรียน
        $studentSummary = $this->attendanceService->getStudentSummary();
        
        return view('attendance.dashboard', [
            'staffSummary' => $staffSummary['summary'] ?? [],
            'staffRecentScans' => $staffSummary['recent_scans'] ?? [],
            'studentSummary' => $studentSummary['summary'] ?? [],
            'studentRecentScans' => $studentSummary['recent_scans'] ?? [],
        ]);
    }

    /**
     * หน้ารายละเอียดการเข้างานข้าราชการ
     */
    public function staffAttendance(Request $request)
    {
        $date = $request->get('date', now()->toDateString());
        $department = $request->get('department');
        
        $attendance = $this->attendanceService->getStaffAttendance($date, $department);
        
        return view('attendance.staff', [
            'date' => $date,
            'data' => $attendance['data'] ?? [],
            'total' => $attendance['total_records'] ?? 0,
        ]);
    }

    /**
     * หน้ารายละเอียดการเข้าเรียนนักเรียน
     */
    public function studentAttendance(Request $request)
    {
        $date = $request->get('date', now()->toDateString());
        $courseId = $request->get('course_id');
        $period = $request->get('period');
        
        $attendance = $this->attendanceService->getStudentAttendance($date, $courseId, $period);
        
        return view('attendance.students', [
            'date' => $date,
            'data' => $attendance['data'] ?? [],
            'total' => $attendance['total_records'] ?? 0,
        ]);
    }

    /**
     * API Endpoint สำหรับ AJAX (Real-time update)
     */
    public function apiStaffSummary()
    {
        $summary = $this->attendanceService->getStaffSummary();
        return response()->json($summary);
    }

    public function apiStudentSummary(Request $request)
    {
        $courseId = $request->get('course_id');
        $summary = $this->attendanceService->getStudentSummary(null, $courseId);
        return response()->json($summary);
    }
}
