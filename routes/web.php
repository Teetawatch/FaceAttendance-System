<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\FaceRegistrationController;
use App\Http\Controllers\StudentFaceRegistrationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Redirect หน้าแรกไป Login
Route::get('/', function () {
    return redirect()->route('login');
});

// Fallback for Shared Hosting: Serve storage files via PHP if symlink fails
// Fallback for Shared Hosting: Serve storage files via PHP if symlink fails
// Usage: route('storage.file', ['path' => 'employees/abc.jpg']) -> /storage-file?path=employees/abc.jpg
Route::get('/storage-file', function (Illuminate\Http\Request $request) {
    $path = $request->query('path');
    if (!$path || !Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
        abort(404);
    }
    // Return with proper MIME type
    return Illuminate\Support\Facades\Storage::disk('public')->response($path);
})->name('storage.file');

// --- Public Kiosk Route (ไม่ต้อง Login) ---
// หน้าสแกนใบหน้าสาธารณะสำหรับใช้เป็น Kiosk ลงเวลา
Route::get('/kiosk', function () {
    return view('monitor.kiosk');
})->name('kiosk');

// Dashboard: ต้อง Login และ Verify Email แล้วเท่านั้น
// Dashboard: ต้อง Login และ Verify Email แล้วเท่านั้น
Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Auth Routes (Login, Register, Logout)
require __DIR__.'/auth.php';

// Routes สำหรับผู้ใช้ที่ Login แล้ว (ทุกคนเข้าได้)
Route::middleware('auth')->group(function () {
    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/signature', [ProfileController::class, 'updateSignature'])->name('profile.signature');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- Phase 7: Employee Self-Service ---
    // เมนู "My Attendance" (ดูประวัติการเข้างานของตัวเอง)
    Route::get('/my-attendance', [AttendanceController::class, 'myAttendance'])->name('attendance.my');
});

// --- Admin Management Routes (Phase 4) ---
// เฉพาะ Admin เท่านั้นที่จัดการ Master Data ได้
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Employee Management
    // Import routes must be defined before resource routes
    Route::get('employees/import', [EmployeeController::class, 'showImportForm'])->name('employees.import.form');
    Route::post('employees/import', [EmployeeController::class, 'import'])->name('employees.import');
    Route::get('employees/template', [EmployeeController::class, 'downloadTemplate'])->name('employees.template');
    Route::resource('employees', EmployeeController::class);
    
    // Device Management
    Route::resource('devices', DeviceController::class);

    // User Management (For managing Verifiers, Approvers, Admins)
    Route::resource('users', \App\Http\Controllers\UserController::class);

    // Face Registration - ลงทะเบียนใบหน้าพนักงานจากกล้อง
    Route::get('/face-register', [FaceRegistrationController::class, 'index'])->name('face.register');
    Route::post('/face-register', [FaceRegistrationController::class, 'store'])->name('face.register.store');
    Route::get('/api/employees/list', [FaceRegistrationController::class, 'getEmployees'])->name('api.employees.list');

    // --- Student Attendance Module ---
    // Course Management (หลักสูตร)
    Route::resource('courses', \App\Http\Controllers\CourseController::class);
    
    // Student Management (นักเรียน)
    // Import routes must be defined before resource routes
    Route::post('students/import', [\App\Http\Controllers\StudentController::class, 'import'])->name('students.import');
    Route::get('students/template', [\App\Http\Controllers\StudentController::class, 'downloadTemplate'])->name('students.template');
    Route::resource('students', \App\Http\Controllers\StudentController::class);

    // Student Face Registration - ลงทะเบียนใบหน้านักเรียนจากกล้อง
    Route::get('/student-face-register', [StudentFaceRegistrationController::class, 'index'])->name('student.face.register');
    Route::post('/student-face-register', [StudentFaceRegistrationController::class, 'store'])->name('student.face.store');
    Route::get('/api/students/list', [StudentFaceRegistrationController::class, 'getStudents'])->name('student.face.api');
});

// --- Monitoring & Reports Routes (Phase 6 & 7) ---
// Admin และ HR สามารถดูหน้า Monitor และรายงานได้
Route::middleware(['auth', 'role:admin,hr'])->group(function () {
    
    // Real-time Monitor Page (Display Mode)
    Route::get('/monitor/display', function () {
        return view('monitor.display');
    })->name('monitor.display');

    // Kiosk Scanning Page (Camera Mode)
    Route::get('/monitor/scan', function () {
        return view('monitor.scan');
    })->name('monitor.scan');

    // Redirect old route
    Route::get('/monitor', function () {
        return redirect()->route('monitor.display');
    })->name('monitor');

    // Attendance Logs for HR (Phase 7)
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::patch('/attendance/{id}', [AttendanceController::class, 'update'])->name('attendance.update');
    
    // Reports & Export (Phase 8)
    Route::get('/reports', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [App\Http\Controllers\ReportController::class, 'export'])->name('reports.export');
    Route::get('/reports/pdf', [App\Http\Controllers\ReportController::class, 'pdf'])->name('reports.pdf');
    
    // Student Reports
    Route::get('/student-reports', [App\Http\Controllers\StudentReportController::class, 'index'])->name('student-reports.index');
    Route::get('/student-reports/export', [App\Http\Controllers\StudentReportController::class, 'export'])->name('student-reports.export');
    Route::get('/student-reports/pdf', [App\Http\Controllers\StudentReportController::class, 'exportPdf'])->name('student-reports.pdf');
    Route::post('/student-reports/send-email', [App\Http\Controllers\StudentReportController::class, 'sendEmail'])->name('student-reports.send-email');
});