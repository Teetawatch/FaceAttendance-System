<?php
/**
 * ตัวอย่างการดึงข้อมูลรายงานจากระบบ Face Attendance
 * สำหรับใช้ในเว็บไซต์ Laravel อื่นบน Host เดียวกัน
 */

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class FaceAttendanceService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        // ตั้งค่า Base URL และ API Key
        $this->baseUrl = config('services.face_attendance.base_url', 'https://nass.ac.th/faceattendance/api/v1/reports');
        $this->apiKey = config('services.face_attendance.api_key');
    }

    /**
     * ดึงข้อมูลเข้า-ออกงานข้าราชการวันนี้
     */
    public function getStaffAttendance(?string $date = null, ?string $department = null, int $limit = 100): array
    {
        $params = [
            'date' => $date ?? now()->toDateString(),
            'limit' => $limit,
        ];

        if ($department) {
            $params['department'] = $department;
        }

        return $this->request('/staff-attendance', $params);
    }

    /**
     * ดึงสรุปการเข้างานข้าราชการ (สำหรับ Dashboard)
     */
    public function getStaffSummary(?string $date = null): array
    {
        $params = ['date' => $date ?? now()->toDateString()];
        
        // Cache ผลลัพธ์ 1 นาที เพื่อลดโหลด
        $cacheKey = 'staff_summary_' . $params['date'];
        
        return Cache::remember($cacheKey, 60, function () use ($params) {
            return $this->request('/staff-summary', $params);
        });
    }

    /**
     * ดึงข้อมูลเข้าเรียนนักเรียนวันนี้
     */
    public function getStudentAttendance(?string $date = null, ?int $courseId = null, ?string $period = null, int $limit = 100): array
    {
        $params = [
            'date' => $date ?? now()->toDateString(),
            'limit' => $limit,
        ];

        if ($courseId) {
            $params['course_id'] = $courseId;
        }

        if ($period) {
            $params['period'] = $period;
        }

        return $this->request('/student-attendance', $params);
    }

    /**
     * ดึงสรุปการเข้าเรียนนักเรียน (สำหรับ Dashboard)
     */
    public function getStudentSummary(?string $date = null, ?int $courseId = null): array
    {
        $params = ['date' => $date ?? now()->toDateString()];
        
        if ($courseId) {
            $params['course_id'] = $courseId;
        }
        
        // Cache ผลลัพธ์ 1 นาที เพื่อลดโหลด
        $cacheKey = 'student_summary_' . $params['date'] . '_' . ($courseId ?? 'all');
        
        return Cache::remember($cacheKey, 60, function () use ($params) {
            return $this->request('/student-summary', $params);
        });
    }

    /**
     * ดึงรายชื่อข้าราชการทั้งหมด
     */
    public function getEmployees(?string $department = null): array
    {
        $params = [];
        
        if ($department) {
            $params['department'] = $department;
        }

        return $this->request('/employees', $params);
    }

    /**
     * ดึงรายชื่อนักเรียนทั้งหมด
     */
    public function getStudents(?int $courseId = null): array
    {
        $params = [];
        
        if ($courseId) {
            $params['course_id'] = $courseId;
        }

        return $this->request('/students', $params);
    }

    /**
     * ส่ง Request ไปยัง API
     */
    protected function request(string $endpoint, array $params = []): array
    {
        try {
            $response = Http::withHeaders([
                'X-API-KEY' => $this->apiKey,
                'Accept' => 'application/json',
            ])->timeout(30)->get($this->baseUrl . $endpoint, $params);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'success' => false,
                'message' => 'API Error: ' . $response->status(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection Error: ' . $e->getMessage(),
            ];
        }
    }
}
