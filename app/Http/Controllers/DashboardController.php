<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\DailyAttendance;
use App\Models\Device;
use App\Models\AttendanceLog;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        // 1. Total Employees
        $totalEmployees = Employee::where('is_active', true)->count();

        // 2. Present Today
        $presentToday = DailyAttendance::whereDate('date', $today)
            ->whereIn('status', ['present', 'late']) // Assuming 'late' is also present
            ->count();

        // 3. Present Yesterday (for comparison)
        $presentYesterday = DailyAttendance::whereDate('date', $yesterday)
            ->whereIn('status', ['present', 'late'])
            ->count();
        
        $presentDiff = $presentToday - $presentYesterday;

        // 4. Late Arrivals
        $lateToday = DailyAttendance::whereDate('date', $today)
            ->where('late_minutes', '>', 0)
            ->count();

        // 5. Devices Online (Assuming 'is_active' means online/available for now)
        // In a real scenario, you might check a 'last_heartbeat' timestamp.
        $totalDevices = Device::count();
        $activeDevices = Device::where('is_active', true)->count();

        // 6. Recent Scans
        $recentScans = AttendanceLog::with(['employee', 'device'])
            ->whereDate('scan_time', $today)
            ->latest('scan_time')
            ->take(5)
            ->get();

        return view('welcome', compact(
            'totalEmployees',
            'presentToday',
            'presentDiff',
            'lateToday',
            'totalDevices',
            'activeDevices',
            'recentScans'
        ));
    }
}
