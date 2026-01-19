<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAttendanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'device_id',
        'scan_type',
        'period',
        'is_late',
        'scan_time',
        'snapshot_path',
    ];

    protected $casts = [
        'scan_time' => 'datetime',
        'is_late' => 'boolean',
    ];

    // นักเรียนที่สแกน
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // อุปกรณ์ที่ใช้สแกน
    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
