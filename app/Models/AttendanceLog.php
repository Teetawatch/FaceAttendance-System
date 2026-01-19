<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'device_id',
        'scan_type',
        'scan_time',
        'is_late',
        'confidence_score',
        'snapshot_path',
    ];

    protected $casts = [
        'scan_time' => 'datetime',
        'is_late' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}