<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'device_code',
        'ip_address',
        'location',
        'api_token',
        'is_active',
    ];

    // Hidden fields (ไม่ส่งกลับไปใน JSON API)
    protected $hidden = [
        'api_token',
    ];

    public function logs()
    {
        return $this->hasMany(AttendanceLog::class);
    }
}