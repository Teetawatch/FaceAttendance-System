<?php

namespace App\Exports;

use App\Models\DailyAttendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = DailyAttendance::with('employee');

        // Filter by Date Range
        if ($this->request->filled('start_date') && $this->request->filled('end_date')) {
            $query->whereBetween('date', [$this->request->start_date, $this->request->end_date]);
        } else {
            // Default: Current Month
            $query->whereMonth('date', Carbon::now()->month)
                  ->whereYear('date', Carbon::now()->year);
        }

        // Filter by Employee
        if ($this->request->filled('employee_id')) {
            $query->where('employee_id', $this->request->employee_id);
        }

        // Filter by Department
        if ($this->request->filled('department')) {
            $department = $this->request->department;
            $query->whereHas('employee', function($q) use ($department) {
                $q->where('department', $department);
            });
        }

        return $query->orderBy('date')->get();
    }

    public function headings(): array
    {
        return [
            'Date',
            'Employee Code',
            'Name',
            'Department',
            'Check In',
            'Check Out',
            'Total Hours',
            'Status',
            'Late Minutes',
        ];
    }

    public function map($attendance): array
    {
        return [
            $attendance->date,
            $attendance->employee->employee_code,
            $attendance->employee->first_name . ' ' . $attendance->employee->last_name,
            $attendance->employee->department,
            $attendance->check_in ? Carbon::parse($attendance->check_in)->format('H:i:s') : '-',
            $attendance->check_out ? Carbon::parse($attendance->check_out)->format('H:i:s') : '-',
            $attendance->total_work_minutes ? number_format($attendance->total_work_minutes / 60, 2) : '-',
            $attendance->status,
            $attendance->late_minutes,
        ];
    }
}
