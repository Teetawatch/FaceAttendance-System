<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ScanController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Endpoint สำหรับเครื่องสแกน (ไม่ต้อง Login แต่ใช้ Token ใน Body แทน)
Route::prefix('v1')->group(function () {
    // Employee (Staff) Endpoints
    Route::post('/scan', [App\Http\Controllers\Api\ScanController::class, 'store'])->name('api.scan.store');
    Route::get('/employees/faces', [App\Http\Controllers\Api\EmployeeController::class, 'getFaces'])->name('api.employees.faces');
    
    // Student Endpoints
    Route::post('/student/scan', [App\Http\Controllers\Api\StudentScanController::class, 'store'])->name('api.student.scan.store');
    Route::get('/students/faces', [App\Http\Controllers\Api\StudentScanController::class, 'getFaces'])->name('api.students.faces');
});

// ============================================
// Report API สำหรับดึงข้อมูลจากเว็บไซต์ภายนอก
// ใช้ API Key ในการยืนยันตัวตน
// ============================================
Route::prefix('v1/reports')->middleware('api.key')->group(function () {
    // รายงานข้าราชการ
    Route::get('/staff-attendance', [App\Http\Controllers\Api\ReportApiController::class, 'staffAttendance'])
        ->name('api.reports.staff-attendance');
    Route::get('/staff-summary', [App\Http\Controllers\Api\ReportApiController::class, 'staffSummary'])
        ->name('api.reports.staff-summary');
    Route::get('/employees', [App\Http\Controllers\Api\ReportApiController::class, 'employees'])
        ->name('api.reports.employees');

    // รายงานนักเรียน
    Route::get('/student-attendance', [App\Http\Controllers\Api\ReportApiController::class, 'studentAttendance'])
        ->name('api.reports.student-attendance');
    Route::get('/student-summary', [App\Http\Controllers\Api\ReportApiController::class, 'studentSummary'])
        ->name('api.reports.student-summary');
    Route::get('/students', [App\Http\Controllers\Api\ReportApiController::class, 'students'])
        ->name('api.reports.students');
});