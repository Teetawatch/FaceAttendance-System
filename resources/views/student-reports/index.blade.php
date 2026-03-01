@extends('layouts.app')

@section('title', 'รายงานนักเรียน')

@section('content')
<div class="space-y-6" x-data="{ showEmailModal: false }">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-text font-bold font-mono font-mono">📊 รายงานการเข้าเรียนนักเรียน</h2>
            <p class="text-indigo-600/70 text-sm">ดูประวัติและสถิติการลงเวลาของนักเรียน</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('student-reports.pdf', array_merge(request()->query(), ['date' => $startDate])) }}" 
               target="_blank"
               class="inline-flex items-center gap-2 bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded-xl transition-all shadow-sm hover:shadow-md text-sm font-medium">
                <x-heroicon-o-document-text class="w-5"/> Export PDF
            </a>
            <button @click="showEmailModal = true" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 shadow-md hover:shadow-lg transition-all text-white px-4 py-2 rounded-xl transition-all shadow-sm hover:shadow-md text-sm font-medium">
                <x-heroicon-o-envelope class="w-5"/> ส่งอีเมล
            </button>
            <a href="{{ route('student-reports.export', request()->query()) }}" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl transition-all shadow-sm hover:shadow-md text-sm font-medium">
                <x-heroicon-o-document-arrow-down class="w-5"/> Export CSV
            </a>
        </div>
    </div>

    <!-- Alert Success -->
     
    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-700 px-4 py-3 rounded-xl border border-emerald-100 flex items-center gap-3 shadow-sm">
            <x-heroicon-o-check-circle class="text-lg w-5"/>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Alert Error -->
    @if(session('error'))
        <div class="bg-rose-50 text-rose-700 px-4 py-3 rounded-xl border border-rose-100 flex items-center gap-3 shadow-sm">
            <x-heroicon-o-x-circle class="text-lg w-5"/>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-card rounded-xl shadow-sm border border-slate-200/60 p-4">
        <form action="{{ route('student-reports.index') }}" method="GET" class="flex flex-col md:flex-row gap-3 items-end">
            <div class="flex-1">
                <label class="text-xs text-indigo-600/70 block mb-1">หลักสูตร</label>
                <select name="course_id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500">
                    <option value="">-- ทุกหลักสูตร --</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ $courseId == $course->id ? 'selected' : '' }}>
                            {{ $course->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs text-indigo-600/70 block mb-1">วันที่เริ่ม</label>
                <input type="date" name="start_date" value="{{ $startDate }}" 
                       class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="text-xs text-indigo-600/70 block mb-1">วันที่สิ้นสุด</label>
                <input type="date" name="end_date" value="{{ $endDate }}" 
                       class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500">
            </div>
            <button type="submit" class="px-6 py-2.5 bg-slate-700 text-white rounded-lg text-sm hover:bg-slate-800 transition-colors">
                <x-heroicon-o-magnifying-glass class="mr-1 w-5"/> ค้นหา
            </button>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
        <div class="bg-card rounded-xl shadow-sm border border-slate-200/60 p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                    <x-heroicon-o-users class="text-blue-600 w-5"/>
                </div>
                <div>
                    <p class="text-sm text-indigo-600/70">นักเรียนทั้งหมด</p>
                    <p class="text-2xl font-bold text-text font-bold font-mono">{{ number_format($totalStudents) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-card rounded-xl shadow-sm border border-slate-200/60 p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center">
                    <x-heroicon-o-user-circle class="text-emerald-600 w-5"/>
                </div>
                <div>
                    <p class="text-sm text-indigo-600/70">มาเรียน (ช่วงเวลา)</p>
                    <p class="text-2xl font-bold text-text font-bold font-mono">{{ number_format($uniqueStudentsCount) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-card rounded-xl shadow-sm border border-rose-100 p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-rose-50 rounded-xl flex items-center justify-center">
                    <x-heroicon-o-x-circle class="text-rose-600 w-5"/>
                </div>
                <div>
                    <p class="text-sm text-indigo-600/70">ยังไม่เข้าเรียน</p>
                    <p class="text-2xl font-bold text-rose-600">{{ number_format($absentCount) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-card rounded-xl shadow-sm border border-amber-100 p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center">
                    <x-heroicon-o-clock class="text-amber-600 w-5"/>
                </div>
                <div>
                    <p class="text-sm text-indigo-600/70">มาสาย</p>
                    <p class="text-2xl font-bold text-amber-600">{{ number_format($lateCount) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-card rounded-xl shadow-sm border border-slate-200/60 p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center">
                    <x-heroicon-o-qr-code class="text-purple-600 w-5"/>
                </div>
                <div>
                    <p class="text-sm text-indigo-600/70">การสแกนทั้งหมด</p>
                    <p class="text-2xl font-bold text-text font-bold font-mono">{{ number_format($totalScansCount) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Late Students Section -->
    @if($lateStudents->count() > 0)
    <div class="bg-amber-50 rounded-2xl shadow-sm border border-amber-100 overflow-hidden" x-data="{ open: true }">
        <button @click="open = !open" class="w-full flex items-center justify-between px-6 py-4 bg-amber-100/50 hover:bg-amber-100 transition-colors">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center">
                    <x-heroicon-o-clock class="text-white w-5"/>
                </div>
                <div class="text-left">
                    <h3 class="text-lg font-bold text-amber-800 font-mono">⏰ นักเรียนที่มาสาย</h3>
                    <p class="text-sm text-amber-600">{{ $lateStudents->unique('student_id')->count() }} คน</p>
                </div>
            </div>
            <x-heroicon-o-chevron-down class="w-5 text-amber-600 transition-transform" :class="{ 'rotate-180': open }"/>
        </button>
        <div x-show="open" x-collapse>
            <div class="p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($lateStudents->unique('student_id') as $log)
                <div class="bg-card rounded-xl p-4 border border-amber-200 flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-amber-100 overflow-hidden flex-shrink-0 border-2 border-amber-300">
                        @if($log->student->photo_path)
                            <img src="{{ route('storage.file', ['path' => $log->student->photo_path]) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-amber-400">
                                <x-heroicon-o-user class="w-5"/>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-text truncate">{{ $log->student->first_name }} {{ $log->student->last_name }}</p>
                        <p class="text-xs text-indigo-600/70">{{ $log->student->student_code }}</p>
                        <p class="text-xs text-amber-600 mt-1">
                            <x-heroicon-o-clock class="mr-1 w-5"/> เข้าเรียนเวลา {{ $log->scan_time->format('H:i:s') }}
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
    <div class="bg-rose-50 rounded-2xl shadow-sm border border-rose-100 overflow-hidden" x-data="{ open: true }">
        <button @click="open = !open" class="w-full flex items-center justify-between px-6 py-4 bg-rose-100/50 hover:bg-rose-100 transition-colors">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-rose-500 rounded-xl flex items-center justify-center">
                    <x-heroicon-o-x-circle class="text-white w-5"/>
                </div>
                <div class="text-left">
                    <h3 class="text-lg font-bold text-rose-800 font-mono">❌ นักเรียนที่ยังไม่เข้าเรียน</h3>
                    <p class="text-sm text-rose-600">{{ $absentStudents->count() }} คน</p>
                </div>
            </div>
            <x-heroicon-o-chevron-down class="w-5 text-rose-600 transition-transform" :class="{ 'rotate-180': open }"/>
        </button>
        <div x-show="open" x-collapse>
            <div class="p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($absentStudents as $student)
                <div class="bg-card rounded-xl p-4 border border-rose-200 flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-rose-100 overflow-hidden flex-shrink-0 border-2 border-rose-300">
                        @if($student->photo_path)
                            <img src="{{ route('storage.file', ['path' => $student->photo_path]) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-rose-400">
                                <x-heroicon-o-user class="w-5"/>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-text truncate">{{ $student->first_name }} {{ $student->last_name }}</p>
                        <p class="text-xs text-indigo-600/70">{{ $student->student_code }}</p>
                        <p class="text-xs text-rose-600 mt-1">
                            <x-heroicon-o-academic-cap class="mr-1 w-5"/> {{ $student->course->name ?? '-' }}
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Data Table -->
    <div class="bg-card rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-text/80">
                <thead class="bg-slate-50/50 text-indigo-600/70 font-semibold border-b border-slate-200/60">
                    <tr>
                        <th class="px-6 py-4">นักเรียน</th>
                        <th class="px-6 py-4">หลักสูตร</th>
                        <th class="px-6 py-4">วันที่</th>
                        <th class="px-6 py-4">เวลา</th>
                        <th class="px-6 py-4 text-center">ประเภท</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($logs as $log)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-slate-100 overflow-hidden flex-shrink-0 border border-slate-200">
                                    @if($log->student->photo_path)
                                        <img src="{{ route('storage.file', ['path' => $log->student->photo_path]) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-300">
                                            <x-heroicon-o-user class="w-5"/>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-bold text-text">{{ $log->student->first_name }} {{ $log->student->last_name }}</p>
                                    <p class="text-xs text-primary-400">{{ $log->student->student_code }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                {{ $log->student->course->name ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-text/80">
                            {{ $log->scan_time->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-mono text-text">{{ $log->scan_time->format('H:i:s') }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-full text-xs font-bold border border-emerald-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                {{ $log->scan_type === 'in' ? 'เข้าเรียน' : 'ออก' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center text-primary-400">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <x-heroicon-o-clipboard-document-list class="text-2xl text-slate-300 w-5"/>
                            </div>
                            <p class="font-medium">ไม่พบข้อมูลการลงเวลา</p>
                            <p class="text-sm mt-1 text-primary-400">ลองเปลี่ยนช่วงเวลาหรือหลักสูตร</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-slate-200/60 bg-slate-50/50">
            {{ $logs->appends(request()->query())->links() }}
        </div>
    </div>

    <!-- Email Modal -->
    <div x-show="showEmailModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         @click.self="showEmailModal = false"
         style="display: none;">
        
        <div x-show="showEmailModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-card rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2 font-mono">
                        <x-heroicon-o-envelope class="w-5"/>
                        ส่งรายงานทางอีเมล
                    </h3>
                    <button @click="showEmailModal = false" class="text-white/80 hover:text-white transition-colors">
                        <x-heroicon-o-x-mark class="text-xl w-5"/>
                    </button>
                </div>
            </div>
            
            <!-- Modal Body -->
            <form action="{{ route('student-reports.send-email') }}" method="POST" class="p-6 space-y-5">
                @csrf
                <!-- Pass current filter params -->
                <input type="hidden" name="course_id" value="{{ $courseId }}">
                <input type="hidden" name="start_date" value="{{ $startDate }}">
                <input type="hidden" name="end_date" value="{{ $endDate }}">
                
                <!-- Email Input -->
                <div>
                    <label class="block text-sm font-medium text-text mb-2">อีเมลปลายทาง <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" required
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                           placeholder="example@email.com">
                    @error('email')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Report Info -->
                <div class="bg-slate-50 rounded-xl p-4 text-sm">
                    <h4 class="font-semibold text-text mb-2 flex items-center gap-2">
                        <x-heroicon-o-information-circle class="text-blue-500 w-5"/>
                        ข้อมูลรายงาน
                    </h4>
                    <ul class="text-text/80 space-y-1 text-xs">
                        <li><strong>หลักสูตร:</strong> {{ $courseId ? $courses->firstWhere('id', $courseId)?->name : 'ทุกหลักสูตร' }}</li>
                        <li><strong>ช่วงวันที่:</strong> {{ $startDate }} ถึง {{ $endDate }}</li>
                        <li><strong>จำนวนรายการ:</strong> {{ $totalScansCount }} รายการ</li>
                    </ul>
                </div>
                
                <!-- Actions -->
                <div class="flex gap-3 pt-2">
                    <button type="button" @click="showEmailModal = false" 
                            class="flex-1 px-4 py-2.5 border border-slate-200 text-text/80 rounded-xl hover:bg-slate-50 transition-colors font-medium">
                        ยกเลิก
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors font-medium flex items-center justify-center gap-2">
                        <x-heroicon-o-star class="w-5"/>
                        ส่งอีเมล
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
