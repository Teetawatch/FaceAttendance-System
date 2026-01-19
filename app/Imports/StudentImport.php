<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\Course;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Throwable;

class StudentImport implements ToModel, WithStartRow, SkipsEmptyRows, SkipsOnError
{
    use Importable;

    protected $importedCount = 0;
    protected $updatedCount = 0;
    protected $skippedRows = [];
    protected $courseId;

    public function __construct($courseId = null)
    {
        $this->courseId = $courseId;
    }

    /**
     * Start from row 2 (skip header row)
     */
    public function startRow(): int
    {
        return 2;
    }

    /**
     * Map Excel row to Student model using column positions
     * Column A (0) = รหัสนักเรียน
     * Column B (1) = ชื่อ  
     * Column C (2) = นามสกุล
     * Column D (3) = ชื่อหลักสูตร (ถ้าไม่ระบุจะใช้ course_id ที่ส่งเข้ามา)
     */
    public function model(array $row)
    {
        // Get values by position (0-indexed)
        $studentCode = isset($row[0]) ? trim($row[0]) : null;
        $firstName = isset($row[1]) ? trim($row[1]) : null;
        $lastName = isset($row[2]) ? trim($row[2]) : null;
        $courseName = isset($row[3]) ? trim($row[3]) : null;

        // Skip if required fields are empty
        if (empty($studentCode) || empty($firstName) || empty($lastName)) {
            return null;
        }

        // Find course by name if provided, otherwise use default courseId
        $courseId = $this->courseId;
        if (!empty($courseName)) {
            $course = Course::where('name', 'like', '%' . $courseName . '%')->first();
            if ($course) {
                $courseId = $course->id;
            }
        }

        // Check if student exists
        $existingStudent = Student::where('student_code', $studentCode)->first();

        if ($existingStudent) {
            // Update existing student
            $existingStudent->update([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'course_id' => $courseId ?? $existingStudent->course_id,
            ]);
            $this->updatedCount++;
            return null;
        }

        // Create new student
        $this->importedCount++;
        
        return new Student([
            'student_code' => $studentCode,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'course_id' => $courseId,
            'is_active' => true,
        ]);
    }

    /**
     * Handle errors during import
     */
    public function onError(Throwable $e)
    {
        \Log::warning('Student Import Error: ' . $e->getMessage());
    }

    /**
     * Get count of newly imported students
     */
    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    /**
     * Get count of updated students
     */
    public function getUpdatedCount(): int
    {
        return $this->updatedCount;
    }

    /**
     * Get skipped rows info
     */
    public function getSkippedRows(): array
    {
        return $this->skippedRows;
    }
}
