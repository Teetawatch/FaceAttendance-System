<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_code',
        'user_id',
        'first_name',
        'last_name',
        'department',
        'position',
        'shift_id',
        'photo_path',
        'is_active',
    ];

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    // ความสัมพันธ์กับ User (ถ้าพนักงานคนนี้มีสิทธิ์ Login)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ประวัติการสแกนทั้งหมด
    public function logs()
    {
        return $this->hasMany(AttendanceLog::class);
    }

    // สรุปรายวัน
    public function dailyAttendances()
    {
        return $this->hasMany(DailyAttendance::class);
    }
}