@extends('layouts.app')

@section('title', 'ประวัติการเข้างาน')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="section-title">ประวัติการเข้างาน</h2>
            <p class="section-subtitle">ตรวจสอบข้อมูลการลงเวลาของพนักงานรายวัน</p>
        </div>
        
        <form method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="relative">
                <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-muted"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ค้นหาชื่อ หรือ รหัสพนักงาน..." 
                       class="pl-9 w-full sm:w-64 rounded-xl border-primary-200 focus:ring-2 focus:ring-primary-100 focus:border-primary-400 text-sm transition-colors duration-150">
            </div>
            
            <input type="date" name="date" value="{{ request('date', date('Y-m-d')) }}" 
                   class="rounded-xl border-primary-200 focus:ring-2 focus:ring-primary-100 focus:border-primary-400 text-sm text-muted transition-colors duration-150">
            
            <button type="submit" class="btn-primary">
                <i data-lucide="filter" class="w-4 h-4"></i> กรองข้อมูล
            </button>
            
            @if(request()->has('date') || request()->has('search'))
                <a href="{{ route('attendance.index') }}" class="btn-ghost">ล้างค่า</a>
            @endif
        </form>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="table-header">
                    <tr>
                        <th class="table-cell">พนักงาน</th>
                        <th class="table-cell">วันที่</th>
                        <th class="table-cell">เข้างาน</th>
                        <th class="table-cell">ออกงาน</th>
                        <th class="table-cell">รวมเวลา</th>
                        <th class="table-cell text-center">สถานะ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary-50/60">
                    @forelse($attendances as $row)
                    <tr class="table-row group">
                        <td class="table-cell">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-surface-50 overflow-hidden flex-shrink-0 border border-primary-100/60 group-hover:border-primary-200 transition-colors duration-150">
                                    @if($row->employee->photo_path)
                                        <img src="{{ route('storage.file', ['path' => $row->employee->photo_path]) }}" class="w-full h-full object-cover" alt="{{ $row->employee->first_name }}">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-muted">
                                            <i data-lucide="user" class="w-4 h-4"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-semibold text-text text-sm group-hover:text-primary-700 transition-colors duration-150">{{ $row->employee->first_name }} {{ $row->employee->last_name }}</p>
                                    <p class="text-xs text-muted font-mono">{{ $row->employee->employee_code }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="table-cell font-mono text-xs text-muted">
                            {{ \Carbon\Carbon::parse($row->date)->locale('th')->isoFormat('D MMM YY') }}
                        </td>
                        <td class="table-cell">
                            @if($row->check_in_at)
                                <span class="badge-success font-mono text-xs">
                                    {{ \Carbon\Carbon::parse($row->check_in_at)->format('H:i') }}
                                </span>
                            @else
                                <span class="text-muted/40">-</span>
                            @endif
                        </td>
                        <td class="table-cell">
                            @if($row->check_out_at)
                                <span class="badge-warning font-mono text-xs">
                                    {{ \Carbon\Carbon::parse($row->check_out_at)->format('H:i') }}
                                </span>
                            @else
                                <span class="text-muted/40">-</span>
                            @endif
                        </td>
                        <td class="table-cell font-mono text-xs text-muted">
                            @if($row->total_work_minutes > 0)
                                {{ floor($row->total_work_minutes / 60) }} ชม. {{ $row->total_work_minutes % 60 }} น.
                            @else
                                -
                            @endif
                        </td>
                        <td class="table-cell text-center">
                            @php
                                $statusConfig = match($row->status) {
                                    'present' => ['label' => 'ปกติ', 'class' => 'badge-success'],
                                    'late' => ['label' => 'สาย', 'class' => 'badge-warning'],
                                    'absent' => ['label' => 'ขาดงาน', 'class' => 'badge-danger'],
                                    'leave' => ['label' => 'ลา', 'class' => 'badge-info'],
                                    default => ['label' => '-', 'class' => 'badge-neutral']
                                };
                            @endphp
                            <span class="{{ $statusConfig['class'] }}">
                                {{ $statusConfig['label'] }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-16 text-center">
                            <div class="w-14 h-14 bg-surface-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                <i data-lucide="clock" class="w-6 h-6 text-muted"></i>
                            </div>
                            <p class="font-medium text-text text-sm">ไม่พบข้อมูลการลงเวลา</p>
                            <p class="text-xs mt-1 text-muted">ลองเปลี่ยนวันที่ หรือคำค้นหา</p>
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



