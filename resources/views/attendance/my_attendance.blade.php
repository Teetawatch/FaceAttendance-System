@extends('layouts.app')

@section('title', 'My Attendance')

@section('content')
<div class="space-y-6">
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-card p-6 rounded-xl shadow-sm border border-slate-200/60 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-green-50 text-green-600 flex items-center justify-center text-xl">
                
            </div>
            <div>
                <p class="text-sm text-indigo-600/70 font-medium">Days Present</p>
                <p class="text-2xl font-bold text-text font-bold font-mono">{{ $stats['present'] }} <span class="text-xs font-normal text-primary-400">this month</span></p>
            </div>
        </div>

        <div class="bg-card p-6 rounded-xl shadow-sm border border-slate-200/60 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                
            </div>
            <div>
                <p class="text-sm text-indigo-600/70 font-medium">Total Hours</p>
                <p class="text-2xl font-bold text-text font-bold font-mono">
                    {{ floor($stats['total_minutes'] / 60) }}<span class="text-sm">h</span> 
                    {{ $stats['total_minutes'] % 60 }}<span class="text-sm">m</span>
                </p>
            </div>
        </div>
        
        <div class="bg-card p-6 rounded-xl shadow-sm border border-slate-200/60 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center text-xl">
                
            </div>
            <div>
                <p class="text-sm text-indigo-600/70 font-medium">Late Arrivals</p>
                <p class="text-2xl font-bold text-text font-bold font-mono">{{ $stats['late'] }} <span class="text-xs font-normal text-primary-400">days</span></p>
            </div>
        </div>
    </div>

    <!-- History List -->
    <div class="bg-card rounded-xl shadow-sm border border-slate-200/60 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200/60 bg-slate-50/50">
            <h3 class="font-bold text-text font-mono">History ({{ date('F Y') }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-text/80">
                <thead class="bg-slate-50 text-text font-semibold uppercase tracking-wider text-xs border-b border-slate-200/60">
                    <tr>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">Check In</th>
                        <th class="px-6 py-4">Check Out</th>
                        <th class="px-6 py-4">Duration</th>
                        <th class="px-6 py-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($attendances as $row)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-text font-bold font-mono">
                            {{ $row->date->format('D, d M Y') }}
                        </td>
                        <td class="px-6 py-4">
                            @if($row->check_in_at)
                                <div class="flex items-center gap-2">
                                    
                                    {{ $row->check_in_at->format('H:i') }}
                                </div>
                            @else
                                <span class="text-primary-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($row->check_out_at)
                                <div class="flex items-center gap-2">
                                    
                                    {{ $row->check_out_at->format('H:i') }}
                                </div>
                            @else
                                <span class="text-primary-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-mono text-xs">
                            @if($row->total_work_minutes > 0)
                                {{ floor($row->total_work_minutes / 60) }}h {{ $row->total_work_minutes % 60 }}m
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($row->status === 'present')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                     Present
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-text/80 uppercase">
                                    {{ $row->status }}
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-primary-400">
                            No attendance history for this month.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200/60">
            {{ $attendances->links() }}
        </div>
    </div>
</div>
@endsection



