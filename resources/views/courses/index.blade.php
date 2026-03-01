@extends('layouts.app')

@section('title', 'จัดการหลักสูตร')

@section('content')
<div class="space-y-6">
    <!-- Header & Action -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-text font-bold font-mono font-mono">หลักสูตรทั้งหมด</h2>
            <p class="text-primary-600/70 text-sm">จัดการหลักสูตรสำหรับนักเรียน</p>
        </div>
        <a href="{{ route('courses.create') }}" class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-xl transition-all shadow-sm hover:shadow-md text-sm font-medium">
            <i class="fa-solid fa-plus"></i> เพิ่มหลักสูตร
        </a>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-700 px-4 py-3 rounded-xl border border-emerald-100 flex items-center gap-3 shadow-sm">
            <i class="fa-solid fa-circle-check text-lg"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Alert Error -->
    @if(session('error'))
        <div class="bg-rose-50 text-rose-700 px-4 py-3 rounded-xl border border-rose-100 flex items-center gap-3 shadow-sm">
            <i class="fa-solid fa-circle-xmark text-lg"></i>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Data Table -->
    <div class="bg-card rounded-2xl shadow-sm border border-primary-50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-text/80">
                <thead class="bg-background/50 text-primary-600/70 font-semibold border-b border-primary-50">
                    <tr>
                        <th class="px-6 py-4">หลักสูตร</th>
                        <th class="px-6 py-4">ระยะเวลา</th>
                        <th class="px-6 py-4 text-center">จำนวนนักเรียน</th>
                        <th class="px-6 py-4 text-center">สถานะ</th>
                        <th class="px-6 py-4 text-right">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($courses as $course)
                    <tr class="hover:bg-background/80 transition-colors group">
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-bold text-text group-hover:text-primary-700 transition-colors">{{ $course->name }}</p>
                                @if($course->description)
                                    <p class="text-xs text-primary-400 mt-1">{{ Str::limit($course->description, 50) }}</p>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm">
                                <p class="text-text/80">{{ $course->start_date->format('d/m/Y') }} - {{ $course->end_date->format('d/m/Y') }}</p>
                                @if($course->isOngoing())
                                    <span class="inline-flex items-center gap-1 text-xs text-emerald-600 mt-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        กำลังดำเนินการ
                                    </span>
                                @elseif($course->end_date < now())
                                    <span class="text-xs text-primary-400">สิ้นสุดแล้ว</span>
                                @else
                                    <span class="text-xs text-blue-500">ยังไม่เริ่ม</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-full text-sm font-bold">
                                <i class="fa-solid fa-users text-xs"></i>
                                {{ $course->students_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($course->is_active)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-full text-xs font-bold border border-emerald-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> เปิดใช้งาน
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 text-text/80 rounded-full text-xs font-bold border border-primary-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> ปิดใช้งาน
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('courses.edit', $course) }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-primary-400 hover:text-primary-600 hover:bg-primary-50 transition-all" title="แก้ไข">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('courses.destroy', $course) }}" method="POST" onsubmit="return confirm('ยืนยันการลบหลักสูตร?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg text-primary-400 hover:text-rose-600 hover:bg-rose-50 transition-all" title="ลบ">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center text-primary-400">
                            <div class="w-16 h-16 bg-background rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fa-solid fa-book text-2xl text-slate-300"></i>
                            </div>
                            <p class="font-medium">ไม่พบหลักสูตร</p>
                            <p class="text-sm mt-1 text-primary-400">เริ่มต้นด้วยการเพิ่มหลักสูตรใหม่</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-primary-50 bg-background/50">
            {{ $courses->links() }}
        </div>
    </div>
</div>
@endsection
