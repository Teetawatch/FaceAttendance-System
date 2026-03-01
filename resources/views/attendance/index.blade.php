@extends('layouts.app')

@section('title', 'ประวัติการเข้างาน')

@section('content')
<div class="space-y-6">
    <!-- Header & Filters -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-text font-bold font-mono font-mono">ประวัติการเข้างาน</h2>
            <p class="text-indigo-600/70 text-sm">ตรวจสอบข้อมูลการลงเวลาของพนักงานรายวัน</p>
        </div>
        
        <form method="GET" class="flex flex-col sm:flex-row gap-3">
            <!-- Search -->
            <div class="relative">
                <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 text-primary-400 text-sm w-5"/>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ค้นหาชื่อ หรือ รหัสพนักงาน..." 
                       class="pl-9 w-full sm:w-64 rounded-xl border-slate-200 focus:ring-primary-500 focus:border-slate-200/600 text-sm shadow-sm">
            </div>
            
            <!-- Date Picker -->
            <input type="date" name="date" value="{{ request('date', date('Y-m-d')) }}" 
                   class="rounded-xl border-slate-200 focus:ring-primary-500 focus:border-slate-200/600 text-sm text-text/80 shadow-sm">
            
            <button type="submit" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 shadow-md hover:shadow-lg transition-all text-white px-4 py-2 rounded-xl transition-all shadow-sm hover:shadow-md text-sm font-medium">
                <x-heroicon-o-funnel class="mr-1 w-5"/> กรองข้อมูล
            </button>
            
            @if(request()->has('date') || request()->has('search'))
                <a href="{{ route('attendance.index') }}" class="flex items-center justify-center px-4 py-2 border border-slate-200 rounded-xl text-indigo-600/70 hover:bg-slate-50 hover:text-text text-sm transition-colors">
                    ล้างค่า
                </a>
            @endif
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-card rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-text/80">
                <thead class="bg-slate-50/50 text-indigo-600/70 font-semibold border-b border-slate-200/60">
                    <tr>
                        <th class="px-6 py-4">พนักงาน</th>
                        <th class="px-6 py-4">วันที่</th>
                        <th class="px-6 py-4">เข้างาน</th>
                        <th class="px-6 py-4">ออกงาน</th>
                        <th class="px-6 py-4">รวมเวลา</th>
                        <th class="px-6 py-4 text-center">สถานะ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($attendances as $row)
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-slate-100 overflow-hidden flex-shrink-0 border border-slate-200 group-hover:border-primary-200 transition-colors">
                                    @if($row->employee->photo_path)
                                        <img src="{{ route('storage.file', ['path' => $row->employee->photo_path]) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-300">
                                            <x-heroicon-o-user class="w-5"/>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-bold text-text group-hover:text-primary-700 transition-colors">{{ $row->employee->first_name }} {{ $row->employee->last_name }}</p>
                                    <p class="text-xs text-primary-400">{{ $row->employee->employee_code }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-mono text-indigo-600/70">
                            {{ \Carbon\Carbon::parse($row->date)->locale('th')->isoFormat('D MMM YY') }}
                        </td>
                        <td class="px-6 py-4">
                            @if($row->check_in_at)
                                <span class="text-emerald-700 font-bold bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-100 font-mono text-xs">
                                    {{ \Carbon\Carbon::parse($row->check_in_at)->format('H:i') }}
                                </span>
                            @else
                                <span class="text-slate-300">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($row->check_out_at)
                                <span class="text-amber-700 font-bold bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-100 font-mono text-xs">
                                    {{ \Carbon\Carbon::parse($row->check_out_at)->format('H:i') }}
                                </span>
                            @else
                                <span class="text-slate-300">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-mono text-indigo-600/70">
                            @if($row->total_work_minutes > 0)
                                {{ floor($row->total_work_minutes / 60) }} ชม. {{ $row->total_work_minutes % 60 }} น.
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $statusConfig = match($row->status) {
                                    'present' => ['label' => 'ปกติ', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-100'],
                                    'late' => ['label' => 'สาย', 'class' => 'bg-amber-50 text-amber-700 border-amber-100'],
                                    'absent' => ['label' => 'ขาดงาน', 'class' => 'bg-rose-50 text-rose-700 border-rose-100'],
                                    'leave' => ['label' => 'ลา', 'class' => 'bg-blue-50 text-blue-700 border-blue-100'],
                                    default => ['label' => '-', 'class' => 'bg-slate-50 text-indigo-600/70 border-slate-200/60']
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $statusConfig['class'] }}">
                                {{ $statusConfig['label'] }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center text-primary-400">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <x-heroicon-o-clipboard-document-list class="text-2xl text-slate-300 w-5"/>
                            </div>
                            <p class="font-medium">ไม่พบข้อมูลการลงเวลา</p>
                            <p class="text-sm mt-1 text-primary-400">ลองเปลี่ยนวันที่ หรือคำค้นหา</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-slate-200/60 bg-slate-50/50">
            {{ $attendances->links() }}
        </div>
    </div>
</div>
@endsection