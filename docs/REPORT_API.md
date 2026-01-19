# Report API Documentation

## Overview
API สำหรับดึงข้อมูลการเข้า-ออกงานของข้าราชการและนักเรียนแบบ Real-time

## Authentication
ทุก Request ต้องมี API Key ส่งมาด้วย โดยสามารถส่งได้ 2 วิธี:

1. **Header** (แนะนำ)
   ```
   X-API-KEY: your-secret-api-key
   ```

2. **Query Parameter**
   ```
   ?api_key=your-secret-api-key
   ```

## Base URL
```
https://nass.ac.th/faceattendance/api/v1/reports
```

---

## Endpoints

### 1. รายงานเข้า-ออกงานข้าราชการ (Real-time)

**GET** `/staff-attendance`

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| date | string | No | วันที่ (Y-m-d) default: วันนี้ |
| department | string | No | กรองตามแผนก |
| employee_id | integer | No | กรองตาม employee_id |
| limit | integer | No | จำนวนรายการ (default: 100, max: 500) |

**Response Example:**
```json
{
    "success": true,
    "date": "2026-01-13",
    "total_records": 25,
    "data": [
        {
            "id": 1,
            "employee_code": "001",
            "full_name": "สมชาย ใจดี",
            "department": "กองบริหาร",
            "position": "หัวหน้าแผนก",
            "scan_type": "in",
            "scan_time": "2026-01-13 07:45:00",
            "is_late": false,
            "device_name": "Kiosk ห้องโถง",
            "snapshot_url": "https://nass.ac.th/faceattendance/storage/snapshots/abc123.jpg"
        }
    ],
    "fetched_at": "2026-01-13 08:00:00"
}
```

---

### 2. สรุปการเข้างานข้าราชการ (Dashboard)

**GET** `/staff-summary`

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| date | string | No | วันที่ (Y-m-d) default: วันนี้ |

**Response Example:**
```json
{
    "success": true,
    "date": "2026-01-13",
    "summary": {
        "total_employees": 50,
        "checked_in": 45,
        "on_time": 40,
        "late": 5,
        "absent": 5,
        "attendance_rate": 90.0
    },
    "recent_scans": [...],
    "fetched_at": "2026-01-13 08:00:00"
}
```

---

### 3. รายงานเข้าเรียนนักเรียน (Real-time)

**GET** `/student-attendance`

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| date | string | No | วันที่ (Y-m-d) default: วันนี้ |
| course_id | integer | No | กรองตามหลักสูตร |
| student_id | integer | No | กรองตาม student_id |
| period | string | No | ช่วงเวลา: 'morning' หรือ 'afternoon' |
| limit | integer | No | จำนวนรายการ (default: 100, max: 500) |

**Response Example:**
```json
{
    "success": true,
    "date": "2026-01-13",
    "total_records": 100,
    "data": [
        {
            "id": 1,
            "student_code": "67001",
            "full_name": "นาย สมศักดิ์ รักเรียน",
            "course_name": "หลักสูตร ปวช.1",
            "scan_type": "in",
            "period": "morning",
            "scan_time": "2026-01-13 07:30:00",
            "is_late": false,
            "device_name": "Kiosk อาคาร A",
            "snapshot_url": "https://nass.ac.th/faceattendance/storage/snapshots/xyz789.jpg"
        }
    ],
    "fetched_at": "2026-01-13 08:00:00"
}
```

---

### 4. สรุปการเข้าเรียนนักเรียน (Dashboard)

**GET** `/student-summary`

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| date | string | No | วันที่ (Y-m-d) default: วันนี้ |
| course_id | integer | No | กรองตามหลักสูตร |

**Response Example:**
```json
{
    "success": true,
    "date": "2026-01-13",
    "course_id": null,
    "summary": {
        "total_students": 200,
        "checked_in": 180,
        "on_time": 170,
        "late": 10,
        "absent": 20,
        "attendance_rate": 90.0
    },
    "recent_scans": [...],
    "fetched_at": "2026-01-13 08:00:00"
}
```

---

### 5. รายชื่อข้าราชการ

**GET** `/employees`

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| department | string | No | กรองตามแผนก |
| is_active | boolean | No | กรองตามสถานะ (default: true) |

---

### 6. รายชื่อนักเรียน

**GET** `/students`

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| course_id | integer | No | กรองตามหลักสูตร |
| is_active | boolean | No | กรองตามสถานะ (default: true) |

---

## Error Response

```json
{
    "success": false,
    "message": "Unauthorized. Invalid API Key."
}
```

---

## Rate Limiting
API มี Rate Limit ที่ 60 requests ต่อนาที ต่อ IP

---

## วิธีตั้งค่า API Key

1. เปิดไฟล์ `.env` ในโปรเจค face-attendance
2. เพิ่มหรือแก้ไขบรรทัด:
   ```
   REPORT_API_KEY=your-secret-api-key-here
   ```
3. สร้าง API Key ที่ปลอดภัย (แนะนำใช้ 32 ตัวอักษรขึ้นไป)
4. นำ API Key ไปใช้ในเว็บไซต์ปลายทาง
