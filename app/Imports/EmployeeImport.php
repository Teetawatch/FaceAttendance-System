<?php

namespace App\Imports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Throwable;

class EmployeeImport implements ToModel, WithStartRow, SkipsEmptyRows, SkipsOnError
{
    use Importable;

    protected $importedCount = 0;
    protected $updatedCount = 0;
    protected $skippedRows = [];

    /**
     * Start from row 2 (skip header row)
     */
    public function startRow(): int
    {
        return 2;
    }

    /**
     * Map Excel row to Employee model using column positions
     * Column A (0) = รหัสพนักงาน
     * Column B (1) = ชื่อจริง  
     * Column C (2) = นามสกุล
     * Column D (3) = แผนก
     * Column E (4) = ตำแหน่ง
     */
    public function model(array $row)
    {
        // Get values by position (0-indexed)
        $employeeCode = isset($row[0]) ? trim($row[0]) : null;
        $firstName = isset($row[1]) ? trim($row[1]) : null;
        $lastName = isset($row[2]) ? trim($row[2]) : null;
        $department = isset($row[3]) ? trim($row[3]) : null;
        $position = isset($row[4]) ? trim($row[4]) : null;

        // Skip if required fields are empty
        if (empty($employeeCode) || empty($firstName) || empty($lastName)) {
            return null;
        }

        // Check if employee exists
        $existingEmployee = Employee::where('employee_code', $employeeCode)->first();

        if ($existingEmployee) {
            // Update existing employee
            $existingEmployee->update([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'department' => $department,
                'position' => $position,
            ]);
            $this->updatedCount++;
            return null;
        }

        // Create new employee
        $this->importedCount++;
        
        return new Employee([
            'employee_code' => $employeeCode,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'department' => $department,
            'position' => $position,
            'is_active' => true,
        ]);
    }

    /**
     * Handle errors during import
     */
    public function onError(Throwable $e)
    {
        \Log::warning('Employee Import Error: ' . $e->getMessage());
    }

    /**
     * Get count of newly imported employees
     */
    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    /**
     * Get count of updated employees
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
