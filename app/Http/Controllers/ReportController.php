<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DailyAttendance;
use App\Models\Employee;
use App\Exports\AttendanceExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // 1. Determine Date Range
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : Carbon::now();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : Carbon::now();

        // 2. Get All Active Employees (Apply filters)
        $employeeQuery = Employee::where('is_active', true)->orderBy('first_name');
        
        if ($request->filled('search')) {
            $search = $request->search;
            $employeeQuery->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%")
                  ->orWhere('employee_code', 'like', "%$search%");
            });
        }

        if ($request->filled('employee_id')) {
            $employeeQuery->where('id', $request->employee_id);
        }
        if ($request->filled('department')) {
            $employeeQuery->where('department', $request->department);
        }
        $employees = $employeeQuery->get();

        // 3. Get Existing Attendances
        $attendanceQuery = DailyAttendance::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
        if ($request->filled('employee_id')) {
            $attendanceQuery->where('employee_id', $request->employee_id);
        }
        $attendancesGrouped = $attendanceQuery->with('employee')->get()->groupBy(function($item) {
            return $item->date->format('Y-m-d') . '_' . $item->employee_id;
        });

        // 4. Build Comprehensive List in Memory
        $reportData = collect();
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $dateStr = $currentDate->format('Y-m-d');
            
            foreach ($employees as $employee) {
                $key = $dateStr . '_' . $employee->id;
                
                if (isset($attendancesGrouped[$key])) {
                    $reportData->push($attendancesGrouped[$key]->first());
                } else {
                    // Create Dummy Object
                    $dummy = new \stdClass();
                    $dummy->id = null; // No ID for dummies (won't be editable unless created)
                    $dummy->date = $dateStr;
                    $dummy->employee = $employee;
                    $dummy->check_in_at = null;
                    $dummy->check_out_at = null;
                    $dummy->total_work_minutes = 0;
                    $dummy->status = 'missing';
                    $dummy->late_minutes = 0;
                    $dummy->remarks = null;
                    $reportData->push($dummy);
                }
            }
            $currentDate->addDay();
        }

        // Sort by Date (Desc) then Employee Name
        $reportData = $reportData->sortByDesc('date')->values();

        // 5. Manual Pagination
        $page = $request->input('page', 1);
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        
        $paginatedItems = new \Illuminate\Pagination\LengthAwarePaginator(
            $reportData->slice($offset, $perPage),
            $reportData->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Data for filters (Reload to pass to view)
        $allEmployees = Employee::where('is_active', true)->orderBy('first_name')->get();
        $departments = Employee::whereNotNull('department')->distinct()->pluck('department');
        $users = \App\Models\User::all(); // For Signature Selection

        return view('reports.index', [
            'attendances' => $paginatedItems,
            'employees' => $allEmployees,
            'departments' => $departments,
            'users' => $users
        ]);
    }

    public function export(Request $request)
    {
        return Excel::download(new AttendanceExport($request), 'attendance_report_' . Carbon::now()->format('Ymd_His') . '.xlsx');
    }

    public function pdf(Request $request)
    {
        // 1. Determine Date Range
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : Carbon::now();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : Carbon::now();
        
        // Limit range to avoid memory issues (e.g., max 31 days) if needed, but for now trust user.
        
        // 2. Get All Active Employees (Apply filters if any)
        $employeeQuery = Employee::where('is_active', true)->orderBy('employee_code');
        
        if ($request->filled('employee_id')) {
            $employeeQuery->where('id', $request->employee_id);
        }
        if ($request->filled('department')) {
            $employeeQuery->where('department', $request->department);
        }
        $employees = $employeeQuery->get();

        // 3. Get Existing Attendances
        $attendanceQuery = DailyAttendance::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
        if ($request->filled('employee_id')) {
            $attendanceQuery->where('employee_id', $request->employee_id);
        }
        $attendances = $attendanceQuery->get()->groupBy(function($item) {
            return $item->date->format('Y-m-d') . '_' . $item->employee_id;
        });

        // 3.1 Fetch Snapshots (Manual Eager Load)
        // Get all IN logs for the date range to avoid N+1
        $logQuery = \App\Models\AttendanceLog::where('scan_type', 'in')
            ->whereBetween('scan_time', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->whereNotNull('snapshot_path');
            
        if ($request->filled('employee_id')) {
            $logQuery->where('employee_id', $request->employee_id);
        }
        
        $snapshots = $logQuery->get()->groupBy(function($item) {
            return $item->scan_time->format('Y-m-d') . '_' . $item->employee_id;
        });

        // 4. Build Comprehensive Report Data
        $reportData = [];
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $dateStr = $currentDate->format('Y-m-d');
            
            foreach ($employees as $employee) {
                $key = $dateStr . '_' . $employee->id;
                
                if (isset($attendances[$key])) {
                    // Existing Record
                    $record = $attendances[$key]->first();
                    
                    // Attach Snapshot if exists
                    if (isset($snapshots[$key])) {
                        // Use the first snapshot found for this day/employee
                        $record->snapshot_path = $snapshots[$key]->first()->snapshot_path;
                    } else {
                        $record->snapshot_path = null;
                    }

                    $reportData[] = $record;
                } else {
                    // Missing Record -> Create Dummy Object for View
                    $dummy = new \stdClass();
                    $dummy->date = $dateStr;
                    $dummy->employee = $employee;
                    $dummy->check_in_at = null;
                    $dummy->check_out_at = null;
                    $dummy->status = 'missing'; // Custom status for "Not Signed In"
                    $dummy->late_minutes = 0;
                    $dummy->snapshot_path = null;
                    $dummy->remarks = null;
                    $reportData[] = $dummy;
                }
            }
            
            $currentDate->addDay();
        }

        // Fetch Signers
        $verifier = null;
        if ($request->filled('verifier_id')) {
            $verifier = \App\Models\User::find($request->verifier_id);
        }

        $approver = null;
        if ($request->filled('approver_id')) {
            $approver = \App\Models\User::find($request->approver_id);
        }

        return view('reports.pdf', [
            'attendances' => $reportData,
            'verifier' => $verifier,
            'approver' => $approver
        ]);
    }
}
