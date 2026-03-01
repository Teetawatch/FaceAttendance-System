@extends('layouts.app')

@section('title', 'จัดการหลักสูตร')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="section-title">หลักสูตรทั้งหมด</h2>
            <p class="section-subtitle">จัดการหลักสูตรสำหรับนักเรียน</p>
        </div>
        <a href="{{ route('courses.create') }}" class="btn-primary">
            <i data-lucide="plus" class="w-4 h-4"></i> เพิ่มหลักสูตร
        </a>
    </div>

    @if(session('success'))
        <div class="alert-success">
            <i data-lucide="check-circle" class="w-4 h-4 flex-shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert-error">
            <i data-lucide="alert-triangle" class="w-4 h-4 flex-shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="table-header">
                    <tr>
                        <th class="table-cell">หลักสูตร</th>
                        <th class="table-cell">ระยะเวลา</th>
                        <th class="table-cell text-center">จำนวนนักเรียน</th>
                        <th class="table-cell text-center">สถานะ</th>
                        <th class="table-cell text-right">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary-50/60">
                    @forelse($courses as $course)
                    <tr class="table-row group">
                        <td class="table-cell">
                            <div>
                                <p class="font-semibold text-text text-sm group-hover:text-primary-700 transition-colors duration-150">{{ $course->name }}</p>
                                @if($course->description)
                                    <p class="text-xs text-muted mt-0.5">{{ Str::limit($course->description, 50) }}</p>
                                @endif
                            </div>
                        </td>
                        <td class="table-cell">
                            <div class="text-sm">
                                <p class="text-muted">{{ $course->start_date->format('d/m/Y') }} - {{ $course->end_date->format('d/m/Y') }}</p>
                                @if($course->isOngoing())
                                    <span class="inline-flex items-center gap-1 text-xs text-emerald-600 mt-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        กำลังดำเนินการ
                                    </span>
                                @elseif($course->end_date < now())
                                    <span class="text-xs text-muted">สิ้นสุดแล้ว</span>
                                @else
                                    <span class="text-xs text-primary-500">ยังไม่เริ่ม</span>
                                @endif
                            </div>
                        </td>
                        <td class="table-cell text-center">
                            <span class="badge-info font-mono">
                                <i data-lucide="users" class="w-3 h-3"></i>
                                {{ $course->students_count }}
                            </span>
                        </td>
                        <td class="table-cell text-center">
                            @if($course->is_active)
                                <span class="badge-success"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> เปิดใช้งาน</span>
                            @else
                                <span class="badge-neutral"><span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> ปิดใช้งาน</span>
                            @endif
                        </td>
                        <td class="table-cell text-right">
                            <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-150">
                                <a href="{{ route('courses.edit', $course) }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-muted hover:text-primary-600 hover:bg-primary-50 transition-colors duration-150 cursor-pointer" title="แก้ไข">
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </a>
                                <form action="{{ route('courses.destroy', $course) }}" method="POST" onsubmit="return confirm('ยืนยันการลบหลักสูตร?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg text-muted hover:text-red-600 hover:bg-red-50 transition-colors duration-150 cursor-pointer" title="ลบ">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-16 text-center">
                            <div class="w-14 h-14 bg-surface-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                <i data-lucide="book-open" class="w-6 h-6 text-muted"></i>
                            </div>
                            <p class="font-medium text-text text-sm">ไม่พบหลักสูตร</p>
                            <p class="text-xs mt-1 text-muted">เริ่มต้นด้วยการเพิ่มหลักสูตรใหม่</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-primary-100/60 bg-surface-50/40">
            {{ $courses->links() }}
        </div>
    </div>
</div>
@endsection




