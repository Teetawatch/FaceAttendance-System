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
        // 1. ตารางพนักงาน
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code')->unique(); // รหัสพนักงาน
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // ผูกกับ Login user (ถ้ามี)
            
            $table->string('first_name');
            $table->string('last_name');
            $table->string('department')->nullable(); // แผนก
            $table->string('position')->nullable(); // ตำแหน่ง
            $table->string('photo_path')->nullable(); // รูปโปรไฟล์/รูปสำหรับ training face
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. ตารางอุปกรณ์สแกน (Devices)
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // ชื่อเครื่อง เช่น "ประตูหน้า"
            $table->string('device_code')->unique(); // รหัสเครื่อง (Unique ID)
            $table->string('ip_address')->nullable();
            $table->string('location')->nullable();
            
            $table->string('api_token')->nullable(); // Token สำหรับให้เครื่องส่งค่ามา
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. ตารางกะการทำงาน (Shifts) - (เผื่อไว้สำหรับ Phase อนาคต)
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // กะเช้า, กะบ่าย
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
        });

        // 4. บันทึกการสแกนดิบ (Attendance Logs)
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('device_id')->nullable()->constrained()->nullOnDelete();
            
            $table->string('scan_type'); // 'in', 'out', 'unknown'
            $table->dateTime('scan_time');
            
            $table->boolean('is_late')->default(false);
            $table->decimal('confidence_score', 5, 2)->nullable(); // ความแม่นยำ Face Rec. (0-100%)
            $table->string('snapshot_path')->nullable(); // รูปที่ capture ได้ขณะสแกน
            
            $table->timestamps();
            
            // Index เพื่อการค้นหาที่รวดเร็ว
            $table->index(['employee_id', 'scan_time']);
        });

        // 5. สรุปการเข้างานรายวัน (Daily Attendances)
        Schema::create('daily_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->date('date'); // วันที่ทำงาน
            
            $table->dateTime('check_in_at')->nullable();
            $table->dateTime('check_out_at')->nullable();
            
            $table->integer('total_work_minutes')->default(0);
            $table->integer('late_minutes')->default(0);
            
            $table->string('status')->default('absent'); // present, absent, leave, late
            
            $table->timestamps();
            
            // ห้ามมี record ซ้ำของพนักงานคนเดิมในวันเดิม
            $table->unique(['employee_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_attendances');
        Schema::dropIfExists('attendance_logs');
        Schema::dropIfExists('shifts');
        Schema::dropIfExists('devices');
        Schema::dropIfExists('employees');
    }
};
