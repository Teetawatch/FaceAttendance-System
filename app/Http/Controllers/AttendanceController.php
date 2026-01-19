<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DailyAttendance;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * สำหรับ HR/Admin: ดูรายการเข้างานของทุกคน
     */
    public function index(Request $request)
    {
        $query = DailyAttendance::with('employee');

        // Filter by Date
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        } else {
            // Default แสดงของวันนี้
            $query->whereDate('date', Carbon::today());
        }

        // Filter by Employee Name/Code
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('employee', function($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%")
                  ->orWhere('employee_code', 'like', "%$search%");
            });
        }

        // เรียงลำดับล่าสุดขึ้นก่อน
        $attendances = $query->latest('updated_at')->paginate(15)->withQueryString();

        return view('attendance.index', compact('attendances'));
    }

    /**
     * สำหรับ Employee: ดูประวัติของตัวเอง
     */
    public function myAttendance()
    {
        $user = Auth::user();
        
        // ตรวจสอบว่า User นี้ผูกกับ Employee หรือยัง
        if (!$user->employee) {
            return redirect()->route('dashboard')->with('error', 'Employee profile not found.');
        }

        // ดึงข้อมูลเดือนปัจจุบัน
        $currentMonth = Carbon::now()->format('Y-m');
        
        $attendances = DailyAttendance::where('employee_id', $user->employee->id)
            ->where('date', 'like', "$currentMonth%")
            ->orderBy('date', 'desc')
            ->paginate(20);

        // คำนวณสถิติเบื้องต้นของเดือนนี้
        $stats = [
            'present' => $attendances->where('status', 'present')->count(),
            'late' => $attendances->sum('late_minutes') > 0 ? $attendances->where('late_minutes', '>', 0)->count() : 0,
            'total_minutes' => $attendances->sum('total_work_minutes'),
        ];

        return view('attendance.my_attendance', compact('attendances', 'stats'));
    }

    /**
     * Store a new attendance record (Manual Entry)
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'status' => 'required|in:present,late,absent,leave,missing',
            'remarks' => 'nullable|string|max:255',
        ]);

        // Check for duplicate
        $exists = DailyAttendance::where('employee_id', $request->employee_id)
            ->whereDate('date', $request->date)
            ->exists();

        if ($exists) {
            return back()->with('error', 'ข้อมูลการลงเวลาของพนักงานคนนี้ในวันที่ระบุมีอยู่แล้ว');
        }

        DailyAttendance::create([
            'employee_id' => $request->employee_id,
            'date' => $request->date,
            'status' => $request->status,
            'remarks' => $request->remarks,
            // Defaults
            'total_work_minutes' => 0,
            'late_minutes' => 0,
        ]);

        return back()->with('success', 'บันทึกข้อมูลเรียบร้อยแล้ว');
    }

    /**
     * Update attendance status and remarks
     */
    public function update(Request $request, $id)
    {
        $attendance = DailyAttendance::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:present,late,absent,leave,missing',
            'remarks' => 'nullable|string|max:255',
        ]);

        $attendance->update([
            'status' => $request->status,
            'remarks' => $request->remarks,
        ]);

        return back()->with('success', 'บันทึกข้อมูลเรียบร้อยแล้ว');
    }
}