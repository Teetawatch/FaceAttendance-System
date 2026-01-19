<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    // นักเรียนในหลักสูตร
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    // ตรวจสอบว่าหลักสูตรกำลังดำเนินอยู่หรือไม่
    public function isOngoing()
    {
        $today = now()->toDateString();
        return $this->start_date <= $today && $this->end_date >= $today;
    }
}
