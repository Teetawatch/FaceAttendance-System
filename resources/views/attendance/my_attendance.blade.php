@extends('layouts.app')

@section('title', 'My Attendance')

@section('content')
<div class="space-y-6">
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="card-hover p-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-xs text-muted font-medium">Days Present</p>
                <p class="text-2xl font-bold text-text font-mono">{{ $stats['present'] }} <span class="text-xs font-normal text-muted">this month</span></p>
            </div>
        </div>

        <div class="card-hover p-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center">
                <i data-lucide="clock" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-xs text-muted font-medium">Total Hours</p>
                <p class="text-2xl font-bold text-text font-mono">
                    {{ floor($stats['total_minutes'] / 60) }}<span class="text-sm">h</span> 
                    {{ $stats['total_minutes'] % 60 }}<span class="text-sm">m</span>
                </p>
            </div>
        </div>
        
        <div class="card-hover p-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <i data-lucide="alert-circle" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-xs text-muted font-medium">Late Arrivals</p>
                <p class="text-2xl font-bold text-text font-mono">{{ $stats['late'] }} <span class="text-xs font-normal text-muted">days</span></p>
            </div>
        </div>
    </div>

    <!-- History List -->
    <div class="card overflow-hidden">
        <div class="px-5 py-4 border-b border-primary-100/60 bg-surface-50/40">
            <h3 class="text-base font-semibold text-text">History ({{ date('F Y') }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="table-header">
                    <tr>
                        <th class="table-cell">Date</th>
                        <th class="table-cell">Check In</th>
                        <th class="table-cell">Check Out</th>
                        <th class="table-cell">Duration</th>
                        <th class="table-cell text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary-50/60">
                    @forelse($attendances as $row)
                    <tr class="table-row">
                        <td class="table-cell font-medium text-text text-sm font-mono">
                            {{ $row->date->format('D, d M Y') }}
                        </td>
                        <td class="table-cell">
                            @if($row->check_in_at)
                                <span class="badge-success font-mono text-xs">{{ $row->check_in_at->format('H:i') }}</span>
                            @else
                                <span class="text-muted/40">-</span>
                            @endif
                        </td>
                        <td class="table-cell">
                            @if($row->check_out_at)
                                <span class="badge-warning font-mono text-xs">{{ $row->check_out_at->format('H:i') }}</span>
                            @else
                                <span class="text-muted/40">-</span>
                            @endif
                        </td>
                        <td class="table-cell font-mono text-xs text-muted">
                            @if($row->total_work_minutes > 0)
                                {{ floor($row->total_work_minutes / 60) }}h {{ $row->total_work_minutes % 60 }}m
                            @else
                                -
                            @endif
                        </td>
                        <td class="table-cell text-center">
                            @if($row->status === 'present')
                                <span class="badge-success">Present</span>
                            @elseif($row->status === 'late')
                                <span class="badge-warning">Late</span>
                            @elseif($row->status === 'absent')
                                <span class="badge-danger">Absent</span>
                            @else
                                <span class="badge-neutral uppercase">{{ $row->status }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center">
                            <div class="w-12 h-12 bg-surface-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                <i data-lucide="calendar-x" class="w-5 h-5 text-muted"></i>
                            </div>
                            <p class="text-sm text-muted">No attendance history for this month.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-primary-100/60 bg-surface-50/40">
            {{ $attendances->links() }}
        </div>
    </div>
</div>
@endsection



