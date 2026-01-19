<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. ตารางหลักสูตร (Courses)
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // ชื่อหลักสูตร
            $table->text('description')->nullable(); // รายละเอียด
            $table->date('start_date'); // วันเริ่มหลักสูตร
            $table->date('end_date'); // วันสิ้นสุดหลักสูตร
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. ตารางนักเรียน (Students)
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_code')->unique(); // รหัสนักเรียน
            $table->string('first_name');
            $table->string('last_name');
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $table->string('photo_path')->nullable(); // รูปสำหรับ Face Recognition
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. บันทึกการสแกนนักเรียน (Student Attendance Logs)
        Schema::create('student_attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('device_id')->nullable()->constrained()->nullOnDelete();
            
            $table->string('scan_type'); // 'in', 'out'
            $table->dateTime('scan_time');
            $table->string('snapshot_path')->nullable(); // รูปที่ capture
            
            $table->timestamps();
            
            // Index สำหรับการค้นหา
            $table->index(['student_id', 'scan_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_attendance_logs');
        Schema::dropIfExists('students');
        Schema::dropIfExists('courses');
    }
};
