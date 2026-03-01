@extends('layouts.app')

@section('title', 'รายงานนักเรียน')

@section('content')
<div class="space-y-6" x-data="{ showEmailModal: false }">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="section-title">รายงานการเข้าเรียนนักเรียน</h2>
            <p class="section-subtitle">ดูประวัติและสถิติการลงเวลาของนักเรียน</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('student-reports.pdf', array_merge(request()->query(), ['date' => $startDate])) }}" 
               target="_blank" class="btn-danger">
                <i data-lucide="file-text" class="w-4 h-4"></i> Export PDF
            </a>
            <button @click="showEmailModal = true" class="btn-primary">
                <i data-lucide="mail" class="w-4 h-4"></i> ส่งอีเมล
            </button>
            <a href="{{ route('student-reports.export', request()->query()) }}" class="btn-accent">
                <i data-lucide="file-spreadsheet" class="w-4 h-4"></i> Export CSV
            </a>
        </div>
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

    <!-- Filters -->
    <div class="card p-4">
        <form action="{{ route('student-reports.index') }}" method="GET" class="flex flex-col md:flex-row gap-3 items-end">
            <div class="flex-1">
                <label class="text-xs text-muted block mb-1.5">หลักสูตร</label>
                <select name="course_id" class="w-full px-4 py-2.5 bg-white border border-primary-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-100 focus:border-primary-400 transition-colors duration-150">
                    <option value="">-- ทุกหลักสูตร --</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ $courseId == $course->id ? 'selected' : '' }}>
                            {{ $course->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs text-muted block mb-1.5">วันที่เริ่ม</label>
                <input type="date" name="start_date" value="{{ $startDate }}" 
                       class="px-4 py-2.5 bg-white border border-primary-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-100 focus:border-primary-400 transition-colors duration-150">
            </div>
            <div>
                <label class="text-xs text-muted block mb-1.5">วันที่สิ้นสุด</label>
                <input type="date" name="end_date" value="{{ $endDate }}" 
                       class="px-4 py-2.5 bg-white border border-primary-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-100 focus:border-primary-400 transition-colors duration-150">
            </div>
            <button type="submit" class="btn-primary">
                <i data-lucide="search" class="w-4 h-4"></i> ค้นหา
            </button>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
        <div class="card-hover p-5">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-primary-50 rounded-xl flex items-center justify-center text-primary-600">
                    <i data-lucide="users" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-muted">นักเรียนทั้งหมด</p>
                    <p class="text-xl font-bold text-text font-mono">{{ number_format($totalStudents) }}</p>
                </div>
            </div>
        </div>
        <div class="card-hover p-5">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600">
                    <i data-lucide="user-check" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-muted">มาเรียน (ช่วงเวลา)</p>
                    <p class="text-xl font-bold text-text font-mono">{{ number_format($uniqueStudentsCount) }}</p>
                </div>
            </div>
        </div>
        <div class="card-hover p-5">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center text-red-600">
                    <i data-lucide="user-x" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-muted">ยังไม่เข้าเรียน</p>
                    <p class="text-xl font-bold text-red-600 font-mono">{{ number_format($absentCount) }}</p>
                </div>
            </div>
        </div>
        <div class="card-hover p-5">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600">
                    <i data-lucide="clock" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-muted">มาสาย</p>
                    <p class="text-xl font-bold text-amber-600 font-mono">{{ number_format($lateCount) }}</p>
                </div>
            </div>
        </div>
        <div class="card-hover p-5">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-primary-50 rounded-xl flex items-center justify-center text-primary-600">
                    <i data-lucide="scan-line" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-muted">การสแกนทั้งหมด</p>
                    <p class="text-xl font-bold text-text font-mono">{{ number_format($totalScansCount) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Late Students Section -->
    @if($lateStudents->count() > 0)
    <div class="bg-amber-50 rounded-2xl border border-amber-100 overflow-hidden" x-data="{ open: true }">
        <button @click="open = !open" class="w-full flex items-center justify-between px-5 py-4 hover:bg-amber-100/50 transition-colors duration-150 cursor-pointer">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-amber-500 rounded-xl flex items-center justify-center text-white">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                </div>
                <div class="text-left">
                    <h3 class="text-sm font-semibold text-amber-800">นักเรียนที่มาสาย</h3>
                    <p class="text-xs text-amber-600">{{ $lateStudents->unique('student_id')->count() }} คน</p>
                </div>
            </div>
            <i data-lucide="chevron-down" class="w-4 h-4 text-amber-500 transition-transform duration-150" :class="open ? 'rotate-180' : ''"></i>
        </button>
        <div x-show="open" x-collapse>
            <div class="p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($lateStudents->unique('student_id') as $log)
                <div class="bg-white rounded-xl p-4 border border-amber-200 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 overflow-hidden flex-shrink-0 border border-amber-200">
                        @if($log->student->photo_path)
                            <img src="{{ route('storage.file', ['path' => $log->student->photo_path]) }}" class="w-full h-full object-cover" alt="{{ $log->student->first_name }}">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-amber-400">
                                <i data-lucide="user" class="w-4 h-4"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-text text-sm truncate">{{ $log->student->first_name }} {{ $log->student->last_name }}</p>
                        <p class="text-xs text-muted">{{ $log->student->student_code }}</p>
                        <p class="text-xs text-amber-600 mt-0.5 flex items-center gap-1">
                            <i data-lucide="clock" class="w-3 h-3"></i> เข้าเรียนเวลา {{ $log->scan_time->format('H:i:s') }}
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Absent Students Section -->
    @if($absentStudents->count() > 0)
    <div class="bg-red-50 rounded-2xl border border-red-100 overflow-hidden" x-data="{ open: true }">
        <button @click="open = !open" class="w-full flex items-center justify-between px-5 py-4 hover:bg-red-100/50 transition-colors duration-150 cursor-pointer">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-red-500 rounded-xl flex items-center justify-center text-white">
                    <i data-lucide="user-x" class="w-4 h-4"></i>
                </div>
                <div class="text-left">
                    <h3 class="text-sm font-semibold text-red-800">นักเรียนที่ยังไม่เข้าเรียน</h3>
                    <p class="text-xs text-red-600">{{ $absentStudents->count() }} คน</p>
                </div>
            </div>
            <i data-lucide="chevron-down" class="w-4 h-4 text-red-500 transition-transform duration-150" :class="open ? 'rotate-180' : ''"></i>
        </button>
        <div x-show="open" x-collapse>
            <div class="p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($absentStudents as $student)
                <div class="bg-white rounded-xl p-4 border border-red-200 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-red-100 overflow-hidden flex-shrink-0 border border-red-200">
                        @if($student->photo_path)
                            <img src="{{ route('storage.file', ['path' => $student->photo_path]) }}" class="w-full h-full object-cover" alt="{{ $student->first_name }}">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-red-400">
                                <i data-lucide="user" class="w-4 h-4"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-text text-sm truncate">{{ $student->first_name }} {{ $student->last_name }}</p>
                        <p class="text-xs text-muted">{{ $student->student_code }}</p>
                        <p class="text-xs text-red-600 mt-0.5 flex items-center gap-1">
                            <i data-lucide="book-open" class="w-3 h-3"></i> {{ $student->course->name ?? '-' }}
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Data Table -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="table-header">
                    <tr>
                        <th class="table-cell">นักเรียน</th>
                        <th class="table-cell">หลักสูตร</th>
                        <th class="table-cell">วันที่</th>
                        <th class="table-cell">เวลา</th>
                        <th class="table-cell text-center">ประเภท</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary-50/60">
                    @forelse($logs as $log)
                    <tr class="table-row group">
                        <td class="table-cell">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-surface-50 overflow-hidden flex-shrink-0 border border-primary-100/60 group-hover:border-primary-200 transition-colors duration-150">
                                    @if($log->student->photo_path)
                                        <img src="{{ route('storage.file', ['path' => $log->student->photo_path]) }}" class="w-full h-full object-cover" alt="{{ $log->student->first_name }}">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-muted">
                                            <i data-lucide="user" class="w-4 h-4"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-semibold text-text text-sm group-hover:text-primary-700 transition-colors duration-150">{{ $log->student->first_name }} {{ $log->student->last_name }}</p>
                                    <p class="text-xs text-muted font-mono">{{ $log->student->student_code }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="table-cell">
                            <span class="badge-info">{{ $log->student->course->name ?? '-' }}</span>
                        </td>
                        <td class="table-cell text-muted text-sm">
                            {{ $log->scan_time->format('d/m/Y') }}
                        </td>
                        <td class="table-cell">
                            <span class="font-mono text-text text-sm">{{ $log->scan_time->format('H:i:s') }}</span>
                        </td>
                        <td class="table-cell text-center">
                            <span class="badge-success">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                {{ $log->scan_type === 'in' ? 'เข้าเรียน' : 'ออก' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-16 text-center">
                            <div class="w-14 h-14 bg-surface-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                <i data-lucide="bar-chart-3" class="w-6 h-6 text-muted"></i>
                            </div>
                            <p class="font-medium text-text text-sm">ไม่พบข้อมูลการลงเวลา</p>
                            <p class="text-xs mt-1 text-muted">ลองเปลี่ยนช่วงเวลาหรือหลักสูตร</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-5 py-4 border-t border-primary-100/60 bg-surface-50/40">
            {{ $logs->appends(request()->query())->links() }}
        </div>
    </div>

    <!-- Email Modal -->
    <div x-show="showEmailModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-primary-950/30 backdrop-blur-sm"
         @click.self="showEmailModal = false"
         style="display: none;">
        
        <div x-show="showEmailModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-2xl shadow-lg border border-primary-100/60 w-full max-w-md overflow-hidden">
            
            <div class="px-6 py-4 border-b border-primary-100/60 flex items-center justify-between">
                <h3 class="text-base font-semibold text-text flex items-center gap-2">
                    <i data-lucide="mail" class="w-5 h-5 text-primary-500"></i>
                    ส่งรายงานทางอีเมล
                </h3>
                <button @click="showEmailModal = false" class="text-muted hover:text-text transition-colors duration-150 cursor-pointer p-1 rounded-lg hover:bg-surface-50">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <form action="{{ route('student-reports.send-email') }}" method="POST" class="p-6 space-y-5">
                @csrf
                <input type="hidden" name="course_id" value="{{ $courseId }}">
                <input type="hidden" name="start_date" value="{{ $startDate }}">
                <input type="hidden" name="end_date" value="{{ $endDate }}">
                
                <div>
                    <label class="block text-sm font-medium text-text mb-2">อีเมลปลายทาง <span class="text-red-500">*</span></label>
                    <input type="email" name="email" required class="input-field" placeholder="example@email.com">
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="bg-surface-50 rounded-xl p-4">
                    <h4 class="font-semibold text-text mb-2 flex items-center gap-2 text-xs">
                        <i data-lucide="info" class="w-4 h-4 text-primary-400"></i>
                        ข้อมูลรายงาน
                    </h4>
                    <ul class="text-muted space-y-1 text-xs">
                        <li><strong class="text-text">หลักสูตร:</strong> {{ $courseId ? $courses->firstWhere('id', $courseId)?->name : 'ทุกหลักสูตร' }}</li>
                        <li><strong class="text-text">ช่วงวันที่:</strong> {{ $startDate }} ถึง {{ $endDate }}</li>
                        <li><strong class="text-text">จำนวนรายการ:</strong> {{ $totalScansCount }} รายการ</li>
                    </ul>
                </div>
                
                <div class="flex gap-3 pt-2">
                    <button type="button" @click="showEmailModal = false" class="btn-secondary flex-1">ยกเลิก</button>
                    <button type="submit" class="btn-primary flex-1">
                        <i data-lucide="send" class="w-4 h-4"></i> ส่งอีเมล
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection




