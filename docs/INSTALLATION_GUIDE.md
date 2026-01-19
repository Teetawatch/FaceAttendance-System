# วิธีติดตั้งและใช้งาน Report API ในเว็บไซต์อื่น

## 📋 ขั้นตอนการติดตั้ง

### 1. ตั้งค่า API Key ในระบบ Face Attendance

เปิดไฟล์ `.env` ของระบบ face-attendance แล้วเพิ่ม:

```env
REPORT_API_KEY=your-secret-api-key-here
```

**สร้าง API Key ที่ปลอดภัย:**
```bash
# ใช้คำสั่งนี้เพื่อสร้าง Key แบบสุ่ม
php artisan tinker --execute="echo bin2hex(random_bytes(32));"
```

---

### 2. ตั้งค่าในเว็บไซต์ปลายทาง (Laravel)

#### 2.1 เพิ่มค่า config ใน `.env`
```env
FACE_ATTENDANCE_API_URL=https://nass.ac.th/faceattendance/api/v1/reports
FACE_ATTENDANCE_API_KEY=your-secret-api-key-here
```

#### 2.2 สร้างไฟล์ config (ถ้ายังไม่มี)
แก้ไข `config/services.php` เพิ่ม:

```php
'face_attendance' => [
    'base_url' => env('FACE_ATTENDANCE_API_URL', 'https://nass.ac.th/faceattendance/api/v1/reports'),
    'api_key' => env('FACE_ATTENDANCE_API_KEY'),
],
```

#### 2.3 คัดลอก Service Class
คัดลอกไฟล์ `FaceAttendanceService.php` ไปวางใน `app/Services/`

#### 2.4 ลงทะเบียน Service (Optional)
ถ้าต้องการใช้แบบ Dependency Injection เพิ่มใน `app/Providers/AppServiceProvider.php`:

```php
use App\Services\FaceAttendanceService;

public function register()
{
    $this->app->singleton(FaceAttendanceService::class, function ($app) {
        return new FaceAttendanceService();
    });
}
```

---

### 3. สร้าง Controller

คัดลอกไฟล์ `AttendanceDashboardController.php` ไปวางใน `app/Http/Controllers/`

---

### 4. สร้าง Routes

เพิ่มใน `routes/web.php`:

```php
use App\Http\Controllers\AttendanceDashboardController;

Route::prefix('attendance')->group(function () {
    Route::get('/', [AttendanceDashboardController::class, 'index'])->name('attendance.dashboard');
    Route::get('/staff', [AttendanceDashboardController::class, 'staffAttendance'])->name('attendance.staff');
    Route::get('/students', [AttendanceDashboardController::class, 'studentAttendance'])->name('attendance.students');
});

// API สำหรับ AJAX
Route::prefix('api/attendance')->group(function () {
    Route::get('/staff-summary', [AttendanceDashboardController::class, 'apiStaffSummary']);
    Route::get('/student-summary', [AttendanceDashboardController::class, 'apiStudentSummary']);
});
```

---

### 5. สร้าง View

คัดลอกไฟล์ `dashboard.blade.php` ไปวางใน `resources/views/attendance/`

---

## 🧪 ทดสอบการเชื่อมต่อ

### ทดสอบด้วย cURL
```bash
curl -X GET "https://nass.ac.th/faceattendance/api/v1/reports/staff-summary" \
     -H "X-API-KEY: your-secret-api-key" \
     -H "Accept: application/json"
```

### ทดสอบใน Tinker (Laravel)
```php
php artisan tinker

use App\Services\FaceAttendanceService;
$service = new FaceAttendanceService();
$result = $service->getStaffSummary();
print_r($result);
```

---

## 📊 API Endpoints ที่พร้อมใช้งาน

| Endpoint | Description |
|----------|-------------|
| GET `/staff-attendance` | รายการเข้า-ออกงานข้าราชการ |
| GET `/staff-summary` | สรุปการเข้างานข้าราชการ |
| GET `/employees` | รายชื่อข้าราชการ |
| GET `/student-attendance` | รายการเข้าเรียนนักเรียน |
| GET `/student-summary` | สรุปการเข้าเรียนนักเรียน |
| GET `/students` | รายชื่อนักเรียน |

---

## ⚠️ ข้อควรระวัง

1. **เก็บ API Key ให้ปลอดภัย** - อย่า commit ใน git หรือเปิดเผยต่อสาธารณะ
2. **Rate Limiting** - API มี limit 60 requests/นาที ต่อ IP
3. **Caching** - แนะนำให้ cache ผลลัพธ์ 1-5 นาที เพื่อลดการเรียก API
4. **Error Handling** - ควรมีการจัดการเมื่อ API ไม่ตอบกลับ

---

## 🔒 Security Tips

1. ใช้ HTTPS เท่านั้น
2. เปลี่ยน API Key เป็นระยะ
3. จำกัด IP ที่เข้าถึงได้ (ถ้าทำได้)
4. ตรวจสอบ logs การใช้งาน API เป็นประจำ
