<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงานการเข้าเรียน - {{ $courseName }}</title>
    
    <!-- Google Fonts: Sarabun -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        @media print {
            .no-print { display: none !important; }
            @page { margin: 0.5cm; size: A4 landscape; }
            body { background: white; }
            .page-break { page-break-inside: avoid; }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen p-8 text-text font-bold font-mono">

    <!-- Print Controls -->
    <div class="no-print fixed top-6 right-6 flex gap-3 z-50">
        <button onclick="window.print()" class="bg-blue-600 text-white px-5 py-2.5 rounded-full shadow-lg hover:bg-blue-700 font-semibold flex items-center gap-2 transition-all hover:scale-105">
            <x-heroicon-o-printer class="w-5"/>
            พิมพ์รายงาน
        </button>
        <button onclick="window.close()" class="bg-card text-text/80 px-5 py-2.5 rounded-full shadow-lg hover:bg-slate-50 font-semibold transition-all">
            ปิด
        </button>
    </div>

    <!-- Filter Controls -->
    <div class="no-print max-w-[1200px] mx-auto mb-6 bg-card rounded-xl shadow-sm border border-slate-200 p-4">
        <form action="{{ route('student-reports.pdf') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1">
                <label class="text-xs text-indigo-600/70 block mb-1">หลักสูตร</label>
                <select name="course_id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">-- ทุกหลักสูตร --</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ $courseId == $course->id ? 'selected' : '' }}>
                            {{ $course->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs text-indigo-600/70 block mb-1">วันที่</label>
                <input type="date" name="date" value="{{ $date }}" 
                       class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" class="px-6 py-2.5 bg-slate-700 text-white rounded-lg text-sm hover:bg-slate-800 transition-colors">
                <x-heroicon-o-magnifying-glass class="mr-1 w-5"/> แสดงรายงาน
            </button>
        </form>
    </div>

    <!-- Report Container -->
    <div class="max-w-[1200px] mx-auto bg-card shadow-xl rounded-lg overflow-hidden print:shadow-none print:w-full print:max-w-none print:rounded-none">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-slate-800 to-slate-700 text-white p-8 print:bg-slate-800">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold mb-1">📊 รายงานการเข้าเรียนนักเรียน</h1>
                    <p class="text-slate-300 text-sm font-light">ระบบลงเวลาด้วย Face Recognition</p>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold text-blue-400">REPORT</div>
                    <p class="text-xs text-primary-400 mt-1">พิมพ์เมื่อ: {{ \Carbon\Carbon::now()->locale('th')->isoFormat('D MMMM YYYY HH:mm') }}</p>
                </div>
            </div>
            
            <!-- Report Info -->
            <div class="mt-6 flex flex-wrap gap-6 text-sm border-t border-slate-600 pt-6">
                <div>
                    <span class="text-primary-400 block text-xs uppercase tracking-wider mb-1">หลักสูตร</span>
                    <span class="font-medium">{{ $courseName }}</span>
                </div>
                <div>
                    <span class="text-primary-400 block text-xs uppercase tracking-wider mb-1">วันที่</span>
                    <span class="font-medium">{{ \Carbon\Carbon::parse($date)->locale('th')->isoFormat('D MMMM YYYY') }}</span>
                </div>
                <div>
                    <span class="text-primary-400 block text-xs uppercase tracking-wider mb-1">จำนวนนักเรียน</span>
                    <span class="font-medium">{{ count($studentLogs) }} คน</span>
                </div>
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-4 border-b border-slate-200 bg-slate-50 print:bg-slate-50">
            <div class="p-4 text-center border-r border-slate-200">
                <div class="text-xs text-blue-600 uppercase font-bold tracking-wider">นักเรียนทั้งหมด</div>
                <div class="text-2xl font-bold text-blue-600 mt-1">{{ $totalStudents }}</div>
            </div>
            <div class="p-4 text-center border-r border-slate-200">
                <div class="text-xs text-emerald-600 uppercase font-bold tracking-wider">ปกติ</div>
                <div class="text-2xl font-bold text-emerald-600 mt-1">{{ $presentCount }}</div>
            </div>
            <div class="p-4 text-center border-r border-slate-200">
                <div class="text-xs text-amber-600 uppercase font-bold tracking-wider">มาสาย</div>
                <div class="text-2xl font-bold text-amber-600 mt-1">{{ $lateCount }}</div>
            </div>
            <div class="p-4 text-center">
                <div class="text-xs text-rose-600 uppercase font-bold tracking-wider">ไม่มาลงชื่อ</div>
                <div class="text-2xl font-bold text-rose-600 mt-1">{{ $absentCount }}</div>
            </div>
        </div>

        <!-- Table -->
        <div class="p-0">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="bg-card border-b-2 border-slate-200/60 text-indigo-600/70 font-semibold uppercase text-xs tracking-wider">
                        <th class="px-3 py-4 w-12 text-center">ลำดับ</th>
                        <th class="px-3 py-4 w-48">ชื่อ-นามสกุล</th>
                        <th class="px-3 py-4 w-24 text-center">เวลาเช้า<br><span class="text-[10px] font-normal text-primary-400">(05:30-08:00)</span></th>
                        <th class="px-3 py-4 w-24 text-center">เวลาบ่าย<br><span class="text-[10px] font-normal text-primary-400">(12:30-13:00)</span></th>
                        <th class="px-3 py-4 w-32 text-center">รูปสแกนเช้า</th>
                        <th class="px-3 py-4 w-32 text-center">รูปสแกนบ่าย</th>
                        <th class="px-3 py-4 w-24 text-center">สถานะ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($studentLogs as $index => $row)
                    <tr class="page-break {{ $index % 2 == 0 ? 'bg-card' : 'bg-slate-50/50' }}">
                        <td class="px-3 py-3 text-center text-primary-400 font-mono text-xs">{{ $index + 1 }}</td>
                        <td class="px-3 py-3">
                            <div class="font-bold text-text font-bold font-mono">{{ $row['student']->first_name }} {{ $row['student']->last_name }}</div>
                            <div class="text-xs text-primary-400 font-mono mt-0.5">{{ $row['student']->student_code }}</div>
                        </td>
                        <td class="px-3 py-3 text-center">
                            @if($row['morning'])
                                <span class="font-mono font-medium {{ $row['morning_late'] ? 'text-amber-600' : 'text-text' }}">
                                    {{ $row['morning']->scan_time->format('H:i:s') }}
                                </span>
                                @if($row['morning_late'])
                                    <span class="block text-[10px] text-amber-600 font-bold">สาย</span>
                                @endif
                            @else
                                <span class="text-slate-300">-</span>
                            @endif
                        </td>
                        <td class="px-3 py-3 text-center">
                            @if($row['afternoon'])
                                <span class="font-mono font-medium {{ $row['afternoon_late'] ? 'text-amber-600' : 'text-text' }}">
                                    {{ $row['afternoon']->scan_time->format('H:i:s') }}
                                </span>
                                @if($row['afternoon_late'])
                                    <span class="block text-[10px] text-amber-600 font-bold">สาย</span>
                                @endif
                            @else
                                <span class="text-slate-300">-</span>
                            @endif
                        </td>
                        <td class="px-3 py-3 text-center">
                            @if($row['morning'] && $row['morning']->snapshot_path)
                                <img src="{{ route('storage.file', ['path' => $row['morning']->snapshot_path]) }}" 
                                     class="w-20 h-20 object-cover rounded-lg border {{ $row['morning_late'] ? 'border-amber-300' : 'border-slate-200' }} mx-auto" alt="Morning">
                            @else
                                <div class="w-20 h-20 bg-slate-100 rounded-lg border border-slate-200 mx-auto flex items-center justify-center text-slate-300">
                                    <x-heroicon-o-photo class="text-xl w-5"/>
                                </div>
                            @endif
                        </td>
                        <td class="px-3 py-3 text-center">
                            @if($row['afternoon'] && $row['afternoon']->snapshot_path)
                                <img src="{{ route('storage.file', ['path' => $row['afternoon']->snapshot_path]) }}" 
                                     class="w-20 h-20 object-cover rounded-lg border {{ $row['afternoon_late'] ? 'border-amber-300' : 'border-slate-200' }} mx-auto" alt="Afternoon">
                            @else
                                <div class="w-20 h-20 bg-slate-100 rounded-lg border border-slate-200 mx-auto flex items-center justify-center text-slate-300">
                                    <x-heroicon-o-photo class="text-xl w-5"/>
                                </div>
                            @endif
                        </td>
                        <td class="px-3 py-3 text-center">
                            @if($row['status'] == 'ปกติ')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                    ปกติ
                                </span>
                            @elseif($row['status'] == 'มาสาย')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                                    มาสาย
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-700 border border-rose-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5"></span>
                                    ไม่มาลงชื่อ
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center text-primary-400 italic">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <x-heroicon-o-clipboard-document-list class="text-2xl text-slate-300 w-5"/>
                            </div>
                            <p class="font-medium">ไม่พบข้อมูลการลงเวลาในวันนี้</p>
                            <p class="text-sm mt-1">ลองเปลี่ยนวันที่หรือหลักสูตร</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="bg-slate-50 border-t border-slate-200 p-6 text-center text-xs text-primary-400 print:bg-card">
            <p>&copy; {{ date('Y') }} ระบบสแกนหน้าเข้าเรียน. All rights reserved.</p>
        </div>
    </div>

</body>
</html>
