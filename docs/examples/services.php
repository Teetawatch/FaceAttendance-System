<?php
/**
 * ตัวอย่างการตั้งค่า config/services.php ในเว็บไซต์อื่น
 */

return [

    // ... ค่าอื่นๆ ...

    /*
    |--------------------------------------------------------------------------
    | Face Attendance API
    |--------------------------------------------------------------------------
    |
    | ตั้งค่าสำหรับเชื่อมต่อกับระบบ Face Attendance
    | เพื่อดึงข้อมูลรายงานการเข้า-ออกงาน
    |
    */

    'face_attendance' => [
        'base_url' => env('FACE_ATTENDANCE_API_URL', 'https://nass.ac.th/faceattendance/api/v1/reports'),
        'api_key' => env('FACE_ATTENDANCE_API_KEY'),
    ],

];
