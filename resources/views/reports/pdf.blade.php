<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงานการลงเวลา</title>
    
    <!-- Google Fonts: Sarabun -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        @media print {
            .no-print { display: none; }
            @page { margin: 0.5cm; size: A4 portrait; }
            body { background: white; }
            .page-break { page-break-inside: avoid; }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen p-8 text-text font-bold font-mono">

    <!-- Print Controls -->
    <div class="no-print fixed top-6 right-6 flex gap-3 z-50">
        <button onclick="window.print()" class="bg-blue-600 text-white px-5 py-2.5 rounded-full shadow-lg hover:bg-blue-700 font-semibold flex items-center gap-2 transition-all hover:scale-105">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            พิมพ์รายงาน
        </button>
        <button onclick="window.close()" class="bg-card text-text/80 px-5 py-2.5 rounded-full shadow-lg hover:bg-slate-50 font-semibold transition-all">
            ปิด
        </button>
    </div>

    <!-- Report Container -->
    <div class="max-w-[297mm] mx-auto bg-card shadow-xl rounded-none md:rounded-lg overflow-hidden print:shadow-none print:w-full print:max-w-none">
        
        <!-- Header -->
        <div class="bg-slate-800 text-white p-8 print:bg-slate-800 print:text-white">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold mb-1">รายงานการลงเวลาปฏิบัติงาน</h1>
                    <p class="text-slate-300 text-sm font-light">โรงเรียนพลาธิการ กรมพลาธิการทหารเรือ</p>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold text-blue-400">REPORT</div>
                    <p class="text-xs text-primary-400 mt-1">พิมพ์เมื่อ: {{ \Carbon\Carbon::now()->locale('th')->isoFormat('D MMMM YYYY HH:mm') }}</p>
                </div>
            </div>
            
            <!-- Report Info -->
            <div class="mt-6 flex flex-wrap gap-6 text-sm border-t border-slate-700 pt-6">
                <div>
                    <span class="text-primary-400 block text-xs uppercase tracking-wider mb-1">ช่วงวันที่</span>
                    <span class="font-medium">
                        {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->locale('th')->isoFormat('D MMM YY') : 'ต้นเดือน' }} 
                        - 
                        {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->locale('th')->isoFormat('D MMM YY') : 'วันนี้' }}
                    </span>
                </div>
                @if(request('department'))
                <div>
                    <span class="text-primary-400 block text-xs uppercase tracking-wider mb-1">แผนก</span>
                    <span class="font-medium">{{ request('department') }}</span>
                </div>
                @endif
                @if(request('employee_id'))
                <div>
                    <span class="text-primary-400 block text-xs uppercase tracking-wider mb-1">ยศ ชื่อ - นามสกุล</span>
                    <span class="font-medium">ระบุรายบุคคล</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Summary Stats -->
        @php
            $total = count($attendances);
            $present = collect($attendances)->where('status', 'present')->count();
            $late = collect($attendances)->where('status', 'late')->count();
            $absent = collect($attendances)->whereIn('status', ['absent', 'missing'])->count();
        @endphp
        <div class="grid grid-cols-4 border-b border-slate-200 bg-slate-50 print:bg-slate-50">
            <div class="p-4 text-center border-r border-slate-200">
                <div class="text-xs text-indigo-600/70 uppercase font-bold tracking-wider">รายการทั้งหมด</div>
                <div class="text-2xl font-bold text-text mt-1">{{ $total }}</div>
            </div>
            <div class="p-4 text-center border-r border-slate-200">
                <div class="text-xs text-emerald-600 uppercase font-bold tracking-wider">ปกติ</div>
                <div class="text-2xl font-bold text-emerald-600 mt-1">{{ $present }}</div>
            </div>
            <div class="p-4 text-center border-r border-slate-200">
                <div class="text-xs text-amber-600 uppercase font-bold tracking-wider">มาสาย</div>
                <div class="text-2xl font-bold text-amber-600 mt-1">{{ $late }}</div>
            </div>
            <div class="p-4 text-center">
                <div class="text-xs text-rose-600 uppercase font-bold tracking-wider">ขาด / ไม่ลงชื่อ</div>
                <div class="text-2xl font-bold text-rose-600 mt-1">{{ $absent }}</div>
            </div>
        </div>

        <!-- Table -->
        <div class="p-0">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="bg-card border-b-2 border-slate-200/60 text-indigo-600/70 font-semibold uppercase text-xs tracking-wider">
                        <th class="px-2 py-4 w-8 text-center">ลำดับ</th>
                        <th class="px-2 py-4 w-20">วันที่</th>
                        <th class="px-2 py-4 w-32">ยศ ชื่อ - นามสกุล</th>
                        <th class="px-2 py-4 w-24">แผนก</th>
                        <th class="px-2 py-4 w-28 text-center">รูปถ่าย</th>
                        <th class="px-2 py-4 w-16 text-center">เวลาเข้า</th>
                        <th class="px-2 py-4 w-24 text-center">สถานะ</th>
                        <th class="px-2 py-4 w-28 text-left">หมายเหตุ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($attendances as $index => $row)
                    <tr class="page-break {{ $index % 2 == 0 ? 'bg-card' : 'bg-slate-50/50' }}">
                        <td class="px-3 py-3 text-center text-primary-400 font-mono text-xs">{{ $index + 1 }}</td>
                        <td class="px-3 py-3 font-medium text-text">
                            {{ \Carbon\Carbon::parse($row->date)->locale('th')->isoFormat('D MMM YY') }}
                        </td>
                        <td class="px-3 py-3">
                            <div class="font-bold text-text font-bold font-mono">{{ $row->employee->first_name }} {{ $row->employee->last_name }}</div>
                            <div class="text-xs text-primary-400 font-mono mt-0.5">{{ $row->employee->employee_code }}</div>
                        </td>
                        <td class="px-3 py-3 text-text/80 text-xs">
                            <span class="bg-slate-100 px-2 py-1 rounded text-indigo-600/70 border border-slate-200">
                                {{ $row->employee->department ?? '-' }}
                            </span>
                        </td>
                        <td class="px-3 py-3 text-center">
                            @if($row->snapshot_path)
                                <img src="{{ route('storage.file', ['path' => $row->snapshot_path]) }}" class="w-24 h-24 object-cover rounded-lg border border-slate-200 mx-auto">
                            @else
                                <div class="w-24 h-24 bg-slate-100 rounded-lg border border-slate-200 mx-auto flex items-center justify-center text-slate-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                        </td>
                        <td class="px-3 py-3 text-center font-mono font-medium text-text">
                            {{ $row->check_in_at ? $row->check_in_at->format('H:i') : '-' }}
                        </td>
                        <td class="px-3 py-3 text-center">
                            @if($row->status == 'present')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                    ปกติ
                                </span>
                            @elseif($row->status == 'late')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-700 border border-amber-200">
                                    สาย ({{ $row->late_minutes }})
                                </span>
                            @elseif($row->status == 'absent')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-700 border border-rose-200">
                                    ขาดงาน
                                </span>
                            @elseif($row->status == 'missing')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-indigo-600/70 border border-slate-200">
                                    ไม่มาลงชื่อ
                                </span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-3 py-3 text-xs text-text/80">
                            {{ $row->remarks }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-primary-400 italic">
                            ไม่พบข้อมูลในช่วงเวลานี้
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Signatures -->
        <!-- Signatures -->
        <!-- Use Table for layout safety in DomPDF -->
        <table style="width: 100%; border-collapse: collapse; margin-top: 2rem;">
            <tr>
                <!-- Verifier Column -->
                <td style="width: 50%; text-align: center; vertical-align: bottom; padding: 0 1rem;">
                    <p style="font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 2rem;">ผู้ตรวจสอบ</p>
                    
                    <div style="position: relative; width: 100%; height: 80px; margin-bottom: -45px; text-align: center;">
                        @if(isset($verifier) && $verifier->signature_path && file_exists(storage_path('app/public/' . str_replace('signatures/', 'signatures/', $verifier->signature_path)))) 
                             @php
                                $path = storage_path('app/public/' . $verifier->signature_path);
                                $type = pathinfo($path, PATHINFO_EXTENSION);
                                $data = file_get_contents($path);
                                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                             @endphp
                            <img src="{{ $base64 }}" style="height: 70px; max-width: 80%; object-fit: contain; display: inline-block;">
                        @endif
                    </div>
                    <div style="border-bottom: 1px dotted #94a3b8; width: 80%; margin: 0 auto; margin-bottom: 0.5rem; position: relative;">
                        <span style="position: absolute; top: -10px; left: 25%; transform: translateX(-50%); background: white; padding: 0 5px; font-size: 0.75rem; color: #64748b;">น.ต.</span>
                    </div>
                    
                    <p style="font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem;">
                        {{ isset($verifier) ? '(' . $verifier->name . ')' : '(.......................................................)' }}
                    </p>
                    <p style="font-size: 0.75rem; color: #64748b;">ฝธก.รร.พธ.พธ.ทร.</p>
                    <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.25rem;">วันที่ {{ \Carbon\Carbon::now()->locale('th')->isoFormat('D MMM YYYY') }}</p>
                </td>

                <!-- Approver Column -->
                <td style="width: 50%; text-align: center; vertical-align: bottom; padding: 0 1rem;">
                    <p style="font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 2rem;">ทราบ</p>
                    
                    <div style="position: relative; width: 100%; height: 80px; margin-bottom: -45px; text-align: center;">
                        @if(isset($approver) && $approver->signature_path && file_exists(storage_path('app/public/' . $approver->signature_path)))
                             @php
                                $path = storage_path('app/public/' . $approver->signature_path);
                                $type = pathinfo($path, PATHINFO_EXTENSION);
                                $data = file_get_contents($path);
                                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                             @endphp
                            <img src="{{ $base64 }}" style="height: 70px; max-width: 80%; object-fit: contain; display: inline-block;">
                        @endif
                    </div>

                    <div style="border-bottom: 1px dotted #94a3b8; width: 80%; margin: 0 auto; margin-bottom: 0.5rem; position: relative;">
                        <span style="position: absolute; top: -10px; left: 25%; transform: translateX(-50%); background: white; padding: 0 5px; font-size: 0.75rem; color: #64748b;">น.อ.</span>
                    </div>
                    
                    <p style="font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem;">
                        {{ isset($approver) ? '(' . $approver->name . ')' : '(.......................................................)' }}
                    </p>
                    <p style="font-size: 0.75rem; color: #64748b;">ผอ.รร.พธ.พธ.ทร.</p>
                    <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.25rem;">วันที่ {{ \Carbon\Carbon::now()->locale('th')->isoFormat('D MMM YYYY') }}</p>
                </td>
            </tr>
        </table>

        <!-- Footer -->
        <div class="bg-slate-50 border-t border-slate-200 p-6 text-center text-xs text-primary-400 print:bg-card">
            <p>&copy; {{ date('Y') }} ระบบสแกนหน้าเข้างาน. All rights reserved.</p>
        </div>
    </div>

</body>
</html>




