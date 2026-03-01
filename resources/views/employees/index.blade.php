@extends('layouts.app')

@section('title', 'จัดการพนักงาน')

@section('content')
<div class="space-y-6" x-data="{ showImportModal: false }">
    <!-- Header & Action -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-text font-bold font-mono font-mono">รายชื่อข้าราชการ ลูกจ้าง และพนักงานราชการ</h2>
            <p class="text-indigo-600/70 text-sm">จัดการข้อมูลพนักงานทั้งหมดในระบบ</p>
        </div>
        <div class="flex items-center gap-3">
            <!-- Search (Optional - Visual for now unless implemented) -->
            <form action="{{ route('employees.index') }}" method="GET" class="relative hidden md:block">
                
                <input type="text" name="search" placeholder="ค้นหาพนักงาน..." value="{{ request('search') }}" 
                       class="pl-9 pr-4 py-2 bg-card border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent shadow-sm w-64">
            </form>

            <!-- Import Excel Button -->
            <button @click="showImportModal = true" type="button" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl transition-all shadow-sm hover:shadow-md text-sm font-medium">
                 Import Excel
            </button>

            <a href="{{ route('employees.create') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 shadow-md hover:shadow-lg transition-all text-white px-4 py-2 rounded-xl transition-all shadow-sm hover:shadow-md text-sm font-medium">
                 เพิ่มพนักงาน
            </a>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-700 px-4 py-3 rounded-xl border border-emerald-100 flex items-center gap-3 shadow-sm">
            
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Alert Error -->
    @if(session('error'))
        <div class="bg-rose-50 text-rose-700 px-4 py-3 rounded-xl border border-rose-100 flex items-start gap-3 shadow-sm">
            
            <div>
                <span class="font-medium">{{ session('error') }}</span>
                @if(session('import_errors'))
                    <ul class="mt-2 text-sm space-y-1 max-h-32 overflow-y-auto">
                        @foreach(session('import_errors') as $importError)
                            <li class="flex items-start gap-1">
                                
                                {{ $importError }}
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    @endif

    <!-- Data Table -->
    <div class="bg-card rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-text/80">
                <thead class="bg-slate-50/50 text-indigo-600/70 font-semibold border-b border-slate-200/60">
                    <tr>
                        <th class="px-6 py-4">พนักงาน</th>
                        <th class="px-6 py-4">รหัส</th>
                        <th class="px-6 py-4">ตำแหน่ง</th>
                        <th class="px-6 py-4">แผนก</th>
                        <th class="px-6 py-4 text-center">สถานะ</th>
                        <th class="px-6 py-4 text-right">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($employees as $employee)
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-slate-100 overflow-hidden flex-shrink-0 border border-slate-200 group-hover:border-primary-200 transition-colors">
                                    @if($employee->photo_path)
                                        <img src="{{ route('storage.file', ['path' => $employee->photo_path]) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-300">
                                            
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-bold text-text group-hover:text-primary-700 transition-colors">{{ $employee->first_name }} {{ $employee->last_name }}</p>
                                    <p class="text-xs text-primary-400">{{ $employee->email ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-mono text-indigo-600/70 bg-slate-50 px-2 py-1 rounded text-xs border border-slate-200/60">{{ $employee->employee_code }}</span>
                        </td>
                        <td class="px-6 py-4 text-text/80">{{ $employee->position ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                {{ $employee->department ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($employee->is_active)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-full text-xs font-bold border border-emerald-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> ปกติ
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 text-text/80 rounded-full text-xs font-bold border border-slate-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> ระงับ
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('employees.edit', $employee) }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-primary-400 hover:text-indigo-600 hover:bg-indigo-50/50 transition-all" title="แก้ไข">
                                    
                                </a>
                                <form action="{{ route('employees.destroy', $employee) }}" method="POST" onsubmit="return confirm('ยืนยันการลบข้อมูลพนักงาน?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg text-primary-400 hover:text-rose-600 hover:bg-rose-50 transition-all" title="ลบ">
                                        
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center text-primary-400">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                
                            </div>
                            <p class="font-medium">ไม่พบข้อมูลพนักงาน</p>
                            <p class="text-sm mt-1 text-primary-400">ลองเพิ่มพนักงานใหม่ หรือค้นหาด้วยคำอื่น</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-slate-200/60 bg-slate-50/50">
            {{ $employees->links() }}
        </div>
    </div>

    <!-- Import Excel Modal -->
    <div x-show="showImportModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         @click.self="showImportModal = false"
         style="display: none;">
        
        <div x-show="showImportModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-card rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-emerald-500 to-teal-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2 font-mono">
                        
                        Import ข้อมูลพนักงานจาก Excel
                    </h3>
                    <button @click="showImportModal = false" class="text-white/80 hover:text-white transition-colors">
                        
                    </button>
                </div>
            </div>
            
            <!-- Modal Body -->
            <form action="{{ route('employees.import') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
                @csrf
                
                <!-- File Upload -->
                <div>
                    <label class="block text-sm font-medium text-text mb-2">เลือกไฟล์ Excel</label>
                    <div class="border-2 border-dashed border-slate-200 rounded-xl p-6 text-center hover:border-emerald-400 transition-colors">
                        <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                               class="block w-full text-sm text-indigo-600/70
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-full file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-emerald-50 file:text-emerald-700
                                      hover:file:bg-emerald-100
                                      cursor-pointer">
                        <p class="text-xs text-primary-400 mt-2">รองรับไฟล์ .xlsx, .xls, .csv (ไม่เกิน 2MB)</p>
                    </div>
                    @error('file')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Format Info -->
                <div class="bg-slate-50 rounded-xl p-4 text-sm">
                    <h4 class="font-semibold text-text mb-2 flex items-center gap-2">
                        
                        รูปแบบไฟล์ที่รองรับ
                    </h4>
                    <ul class="text-text/80 space-y-1 text-xs">
                        <li><strong>คอลัมน์ A:</strong> รหัสพนักงาน (จำเป็น)</li>
                        <li><strong>คอลัมน์ B:</strong> ชื่อจริง (จำเป็น)</li>
                        <li><strong>คอลัมน์ C:</strong> นามสกุล (จำเป็น)</li>
                        <li><strong>คอลัมน์ D:</strong> แผนก</li>
                        <li><strong>คอลัมน์ E:</strong> ตำแหน่ง</li>
                    </ul>
                    <p class="text-xs text-amber-600 mt-2">
                        
                        หากรหัสพนักงานซ้ำ ระบบจะอัพเดตข้อมูลพนักงานคนนั้นอัตโนมัติ
                    </p>
                </div>
                
                <!-- Download Template -->
                <a href="{{ route('employees.template') }}" class="flex items-center justify-center gap-2 text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                    
                    ดาวน์โหลด Template ตัวอย่าง
                </a>
                
                <!-- Actions -->
                <div class="flex gap-3 pt-2">
                    <button type="button" @click="showImportModal = false" 
                            class="flex-1 px-4 py-2.5 border border-slate-200 text-text/80 rounded-xl hover:bg-slate-50 transition-colors font-medium">
                        ยกเลิก
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2.5 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition-colors font-medium flex items-center justify-center gap-2">
                        
                        นำเข้าข้อมูล
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection



