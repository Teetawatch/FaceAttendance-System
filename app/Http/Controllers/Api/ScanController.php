<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\Employee;
use App\Models\AttendanceLog;
use App\Models\DailyAttendance;
use App\Events\NewScan; // Event สำหรับ Real-time Monitor
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScanController extends Controller
{
    /**
     * Handle incoming scan request from device
     */
    public function store(Request $request)
    {
        // 1. Validation เบื้องต้น
        $request->validate([
            'device_code' => 'required|string',
            'api_token' => 'required|string',
            'employee_code' => 'required|string',
        ]);

        // 2. Authenticate Device (ตรวจสอบว่าเครื่องนี้มีสิทธิ์ยิง API ไหม)
        $device = Device::where('device_code', $request->device_code)
            ->where('api_token', $request->api_token)
            ->where('is_active', true)
            ->first();

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized Device or Invalid Token',
            ], 401);
        }

        // 3. Find Employee
        $employee = Employee::where('employee_code', $request->employee_code)
            ->where('is_active', true)
            ->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found',
            ], 404);
        }

        // 4. Process Logic (ใช้ DB Transaction เพื่อความชัวร์ของข้อมูล)
        try {
            return DB::transaction(function () use ($request, $device, $employee) {

                $scanTime = $request->timestamp ? Carbon::parse($request->timestamp) : now();
                $todayDate = $scanTime->format('Y-m-d');

                // --- Step A: Determine Scan Type (In Only) ---
                // ค้นหาว่าวันนี้พนักงานคนนี้มีการบันทึกไว้หรือยัง
                $dailyRecord = DailyAttendance::firstOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'date' => $todayDate
                    ],
                    [
                        'status' => 'absent' // ค่าเริ่มต้น
                    ]
                );

                $scanType = 'unknown';

                if (is_null($dailyRecord->check_in_at)) {
                    // ถ้ายังไม่มีเวลาเข้า -> นี่คือ Check In
                    $dailyRecord->check_in_at = $scanTime;
                    $dailyRecord->status = 'present';
                    $scanType = 'in';

                    // --- Late Detection Logic ---
                    $shift = $employee->shift;

                    // Fallback: If no shift assigned, try to find the "General Shift" or default
                    if (!$shift) {
                        $shift = \App\Models\Shift::where('name', 'General Shift')->first();
                    }

                    if ($shift && $shift->start_time) {
                        // Create Carbon instance for Shift Start Time on Today
                        $shiftStart = Carbon::parse($todayDate . ' ' . $shift->start_time->format('H:i:s'));

                        // Add buffer (e.g., 1 minute grace period) if needed, currently strict
                        if ($scanTime->gt($shiftStart)) {
                            $lateMinutes = $scanTime->diffInMinutes($shiftStart);
                            $dailyRecord->late_minutes = $lateMinutes;
                            $dailyRecord->status = 'late'; // Mark as Late
                        }
                    }
                } else {
                    // ถ้ามีเวลาเข้าแล้ว -> ไม่อนุญาตให้สแกนซ้ำ (ตาม Requirement: เข้าได้แค่ 1 ครั้ง)
                    $lastLog = AttendanceLog::where('employee_id', $employee->id)
                        ->whereDate('scan_time', $todayDate)
                        ->latest()
                        ->first();

                    return response()->json([
                        'success' => true, // Return true so client doesn't show error
                        'message' => 'วันนี้คุณลงเวลาเข้างานเรียบร้อยแล้ว',
                        'data' => [
                            'name' => $employee->first_name . ' ' . $employee->last_name,
                            'employee_code' => $employee->employee_code,
                            'photo_url' => $employee->photo_path ? route('storage.file', ['path' => $employee->photo_path]) : null,
                            'snapshot_url' => ($lastLog && $lastLog->snapshot_path) ? route('storage.file', ['path' => $lastLog->snapshot_path]) : null,
                            'scan_type' => 'เข้างานแล้ว',
                            'time' => $dailyRecord->check_in_at->format('H:i:s'),
                            'status_text' => 'เข้างานแล้ว',
                            'is_late' => false
                        ]
                    ]);
                }

                $dailyRecord->save();

                // --- Step B: Save Raw Log ---
                $snapshotPath = null;
                if ($request->filled('snapshot')) {
                    try {
                        $image = $request->input('snapshot');
                        $image = str_replace('data:image/jpeg;base64,', '', $image);
                        $image = str_replace(' ', '+', $image);
                        $imageName = 'scan_' . time() . '_' . \Illuminate\Support\Str::random(10) . '.jpg';

                        \Illuminate\Support\Facades\Storage::disk('public')->put('snapshots/' . $imageName, base64_decode($image));
                        $snapshotPath = 'snapshots/' . $imageName;
                    } catch (\Exception $e) {
                        Log::error("Snapshot save failed: " . $e->getMessage());
                    }
                }

                $isLate = ($scanType === 'in' && $dailyRecord->late_minutes > 0);

                // Determine Thai Status Text
                $statusText = 'เข้างานปกติ';
                if ($isLate) {
                    $statusText = 'มาสาย (' . $dailyRecord->late_minutes . ' นาที)';
                }

                $log = AttendanceLog::create([
                    'employee_id' => $employee->id,
                    'device_id' => $device->id,
                    'scan_type' => $scanType,
                    'scan_time' => $scanTime,
                    'is_late' => $isLate,
                    'confidence_score' => $request->input('confidence', 0),
                    'snapshot_path' => $snapshotPath,
                ]);

                // Append Thai Text to Log Object for Event
                $log->status_text = $statusText;

                // --- Step C: Trigger Real-time Event ---
                try {
                    // ส่ง Event ไปยัง Pusher เพื่อให้หน้า Monitor อัปเดตทันที
                    NewScan::dispatch($log);
                } catch (\Exception $e) {
                    // ถ้าส่ง Event ไม่ผ่าน (เช่น เน็ตหลุด) ก็แค่ Log ไว้ แต่ไม่ต้อง Rollback Transaction
                    Log::error("Broadcast failed: " . $e->getMessage());
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Scan recorded successfully',
                    'data' => [
                        'name' => $employee->first_name . ' ' . $employee->last_name,
                        'employee_code' => $employee->employee_code,
                        'photo_url' => $employee->photo_path ? route('storage.file', ['path' => $employee->photo_path]) : null,
                        'snapshot_url' => $snapshotPath ? route('storage.file', ['path' => $snapshotPath]) : null,
                        'scan_type' => $statusText,
                        'time' => $scanTime->format('H:i:s'),
                        'status_text' => $statusText,
                        'is_late' => $isLate
                    ]
                ]);
            });

        } catch (\Exception $e) {
            Log::error("Scan Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server Error processing scan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}