<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_code',
        'first_name',
        'last_name',
        'course_id',
        'photo_path',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // หลักสูตรที่สังกัด
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // บันทึกการสแกน
    public function attendanceLogs()
    {
        return $this->hasMany(StudentAttendanceLog::class);
    }

    // ชื่อเต็ม
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
