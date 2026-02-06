<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\Student;
use App\Models\StudentAttendanceLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentScanController extends Controller
{
    /**
     * Handle incoming scan request for students
     * 
     * Two check-in periods:
     * - Morning: 05:30 - 08:00 (late after 08:00)
     * - Afternoon: 12:30 - 13:00 (late after 13:00)
     */
    public function store(Request $request)
    {
        // 1. Validation
        $request->validate([
            'device_code' => 'required|string',
            'api_token' => 'required|string',
            'student_code' => 'required|string',
        ]);

        // 2. Authenticate Device
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

        // 3. Find Student
        $student = Student::with('course')
            ->where('student_code', $request->student_code)
            ->where('is_active', true)
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found',
            ], 404);
        }

        // Check if student's course is still active/ongoing
        if ($student->course) {
            $today = now()->toDateString();
            if ($student->course->end_date < $today) {
                return response()->json([
                    'success' => false,
                    'message' => 'หลักสูตรสิ้นสุดแล้ว',
                ], 400);
            }
        }

        // 4. Process Logic
        try {
            return DB::transaction(function () use ($request, $device, $student) {

                $scanTime = $request->timestamp ? Carbon::parse($request->timestamp) : now();
                $todayDate = $scanTime->format('Y-m-d');
                $currentTime = $scanTime->format('H:i:s');

                // Define check-in periods
                $morningStart = '05:30:00';
                $morningEnd = '08:00:00';
                $morningLateAfter = '08:00:00';

                $afternoonStart = '12:30:00';
                $afternoonEnd = '13:00:00';
                $afternoonLateAfter = '13:00:00';

                // Determine which period this scan belongs to
                $period = null;
                $isLate = false;

                if ($currentTime >= $morningStart && $currentTime < '12:00:00') {
                    // Morning period (05:30 - before 12:00)
                    $period = 'morning';
                    $isLate = $currentTime > $morningLateAfter;
                } elseif ($currentTime >= $afternoonStart && $currentTime < '18:00:00') {
                    // Afternoon period (12:30 - before 18:00)
                    $period = 'afternoon';
                    $isLate = $currentTime > $afternoonLateAfter;
                } else {
                    // Outside allowed check-in time
                    return response()->json([
                        'success' => false,
                        'message' => 'ไม่อยู่ในช่วงเวลาลงเวลาเข้าเรียน (เช้า: 05:30-08:00, บ่าย: 12:30-13:00)',
                    ], 400);
                }

                // Check if already scanned for this period today
                $existingLog = StudentAttendanceLog::where('student_id', $student->id)
                    ->whereDate('scan_time', $todayDate)
                    ->where('period', $period)
                    ->first();

                if ($existingLog) {
                    $periodText = $period === 'morning' ? 'ช่วงเช้า' : 'ช่วงบ่าย';
                    return response()->json([
                        'success' => true,
                        'message' => "วันนี้คุณลงเวลา{$periodText}เรียบร้อยแล้ว",
                        'data' => [
                            'name' => $student->first_name . ' ' . $student->last_name,
                            'student_code' => $student->student_code,
                            'photo_url' => $student->photo_path ? route('storage.file', ['path' => $student->photo_path]) : null,
                            'snapshot_url' => $existingLog->snapshot_path ? route('storage.file', ['path' => $existingLog->snapshot_path]) : null,
                            'scan_type' => 'เข้าเรียนแล้ว',
                            'time' => $existingLog->scan_time->format('H:i:s'),
                            'status_text' => $existingLog->is_late ? 'สาย' : 'ตรงเวลา',
                            'period' => $periodText,
                            'course' => $student->course?->name ?? '-'
                        ]
                    ]);
                }

                // Save snapshot if provided
                $snapshotPath = null;
                if ($request->filled('snapshot')) {
                    try {
                        $image = $request->input('snapshot');
                        $image = str_replace('data:image/jpeg;base64,', '', $image);
                        $image = str_replace(' ', '+', $image);
                        $imageName = 'student_scan_' . time() . '_' . \Illuminate\Support\Str::random(10) . '.jpg';

                        \Illuminate\Support\Facades\Storage::disk('public')->put('snapshots/' . $imageName, base64_decode($image));
                        $snapshotPath = 'snapshots/' . $imageName;
                    } catch (\Exception $e) {
                        Log::error("Snapshot save failed: " . $e->getMessage());
                    }
                }

                // Create attendance log
                $log = StudentAttendanceLog::create([
                    'student_id' => $student->id,
                    'device_id' => $device->id,
                    'scan_type' => 'in',
                    'scan_time' => $scanTime,
                    'snapshot_path' => $snapshotPath,
                    'period' => $period,
                    'is_late' => $isLate,
                ]);

                $periodText = $period === 'morning' ? 'ช่วงเช้า' : 'ช่วงบ่าย';
                $statusText = $isLate ? 'สาย' : 'ตรงเวลา';

                return response()->json([
                    'success' => true,
                    'message' => 'Scan recorded successfully',
                    'data' => [
                        'name' => $student->first_name . ' ' . $student->last_name,
                        'student_code' => $student->student_code,
                        'photo_url' => $student->photo_path ? route('storage.file', ['path' => $student->photo_path]) : null,
                        'snapshot_url' => $snapshotPath ? route('storage.file', ['path' => $snapshotPath]) : null,
                        'scan_type' => "เข้าเรียน{$periodText}",
                        'time' => $scanTime->format('H:i:s'),
                        'status_text' => $statusText,
                        'period' => $periodText,
                        'is_late' => $isLate,
                        'course' => $student->course?->name ?? '-'
                    ]
                ]);
            });

        } catch (\Exception $e) {
            Log::error("Student Scan Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server Error processing scan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get student faces for Kiosk face recognition
     */
    public function getFaces()
    {
        $students = Student::with('course')
            ->where('is_active', true)
            ->whereNotNull('photo_path')
            ->whereHas('course', function ($query) {
                $query->where('is_active', true)
                    ->where('end_date', '>=', now()->toDateString());
            })
            ->get();

        $data = $students->map(function ($student) {
            $photoUrl = $student->photo_path
                ? route('storage.file', ['path' => $student->photo_path])
                : null;

            return [
                'id' => $student->id,
                'student_code' => $student->student_code,
                'name' => $student->first_name . ' ' . $student->last_name,
                'course' => $student->course?->name ?? '-',
                'photo_url' => $photoUrl,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
