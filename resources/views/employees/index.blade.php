@extends('layouts.app')

@section('title', 'จัดการพนักงาน')

@section('content')
<div class="space-y-6" x-data="{ showImportModal: false }">
    <!-- Header & Action -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="section-title">รายชื่อข้าราชการ ลูกจ้าง และพนักงานราชการ</h2>
            <p class="section-subtitle">จัดการข้อมูลพนักงานทั้งหมดในระบบ</p>
        </div>
        <div class="flex items-center gap-3">
            <form action="{{ route('employees.index') }}" method="GET" class="relative hidden md:block">
                <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-muted"></i>
                <input type="text" name="search" placeholder="ค้นหาพนักงาน..." value="{{ request('search') }}" 
                       class="pl-9 pr-4 py-2.5 bg-white border border-primary-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-100 focus:border-primary-400 w-64 transition-colors duration-150">
            </form>

            <button @click="showImportModal = true" type="button" class="btn-accent">
                <i data-lucide="file-spreadsheet" class="w-4 h-4"></i> Import Excel
            </button>

            <a href="{{ route('employees.create') }}" class="btn-primary">
                <i data-lucide="plus" class="w-4 h-4"></i> เพิ่มพนักงาน
            </a>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="alert-success">
            <i data-lucide="check-circle" class="w-4 h-4 flex-shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Alert Error -->
    @if(session('error'))
        <div class="alert-error">
            <i data-lucide="alert-triangle" class="w-4 h-4 flex-shrink-0 mt-0.5"></i>
            <div>
                <span>{{ session('error') }}</span>
                @if(session('import_errors'))
                    <ul class="mt-2 space-y-1 max-h-32 overflow-y-auto text-xs">
                        @foreach(session('import_errors') as $importError)
                            <li class="flex items-start gap-1">
                                <i data-lucide="x" class="w-3 h-3 mt-0.5 flex-shrink-0"></i>
                                {{ $importError }}
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    @endif

    <!-- Data Table -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="table-header">
                    <tr>
                        <th class="table-cell">พนักงาน</th>
                        <th class="table-cell">รหัส</th>
                        <th class="table-cell">ตำแหน่ง</th>
                        <th class="table-cell">แผนก</th>
                        <th class="table-cell text-center">สถานะ</th>
                        <th class="table-cell text-right">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary-50/60">
                    @forelse($employees as $employee)
                    <tr class="table-row group">
                        <td class="table-cell">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-surface-50 overflow-hidden flex-shrink-0 border border-primary-100/60 group-hover:border-primary-200 transition-colors duration-150">
                                    @if($employee->photo_path)
                                        <img src="{{ route('storage.file', ['path' => $employee->photo_path]) }}" class="w-full h-full object-cover" alt="{{ $employee->first_name }}">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-muted">
                                            <i data-lucide="user" class="w-4 h-4"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-semibold text-text text-sm group-hover:text-primary-700 transition-colors duration-150">{{ $employee->first_name }} {{ $employee->last_name }}</p>
                                    <p class="text-xs text-muted">{{ $employee->email ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="table-cell">
                            <span class="font-mono text-primary-600 bg-primary-50 px-2 py-1 rounded-lg text-xs">{{ $employee->employee_code }}</span>
                        </td>
                        <td class="table-cell text-muted">{{ $employee->position ?? '-' }}</td>
                        <td class="table-cell">
                            <span class="badge-info">
                                {{ $employee->department ?? '-' }}
                            </span>
                        </td>
                        <td class="table-cell text-center">
                            @if($employee->is_active)
                                <span class="badge-success">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> ปกติ
                                </span>
                            @else
                                <span class="badge-neutral">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> ระงับ
                                </span>
                            @endif
                        </td>
                        <td class="table-cell text-right">
                            <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-150">
                                <a href="{{ route('employees.edit', $employee) }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-muted hover:text-primary-600 hover:bg-primary-50 transition-colors duration-150 cursor-pointer" title="แก้ไข">
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </a>
                                <form action="{{ route('employees.destroy', $employee) }}" method="POST" onsubmit="return confirm('ยืนยันการลบข้อมูลพนักงาน?');">
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
                        <td colspan="6" class="px-5 py-16 text-center">
                            <div class="w-14 h-14 bg-surface-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                <i data-lucide="users" class="w-6 h-6 text-muted"></i>
                            </div>
                            <p class="font-medium text-text text-sm">ไม่พบข้อมูลพนักงาน</p>
                            <p class="text-xs mt-1 text-muted">ลองเพิ่มพนักงานใหม่ หรือค้นหาด้วยคำอื่น</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-5 py-4 border-t border-primary-100/60 bg-surface-50/40">
            {{ $employees->links() }}
        </div>
    </div>

    <!-- Import Excel Modal -->
    <div x-show="showImportModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-primary-950/30 backdrop-blur-sm"
         @click.self="showImportModal = false"
         style="display: none;">
        
        <div x-show="showImportModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-2xl shadow-lg border border-primary-100/60 w-full max-w-md overflow-hidden">
            
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-primary-100/60 flex items-center justify-between">
                <h3 class="text-base font-semibold text-text flex items-center gap-2">
                    <i data-lucide="file-spreadsheet" class="w-5 h-5 text-accent-500"></i>
                    Import ข้อมูลพนักงานจาก Excel
                </h3>
                <button @click="showImportModal = false" class="text-muted hover:text-text transition-colors duration-150 cursor-pointer p-1 rounded-lg hover:bg-surface-50">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <!-- Modal Body -->
            <form action="{{ route('employees.import') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
                @csrf
                
                <!-- File Upload -->
                <div>
                    <label class="block text-sm font-medium text-text mb-2">เลือกไฟล์ Excel</label>
                    <div class="border-2 border-dashed border-primary-200 rounded-xl p-6 text-center hover:border-primary-400 transition-colors duration-150">
                        <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                               class="block w-full text-sm text-muted
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-full file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-primary-50 file:text-primary-700
                                      hover:file:bg-primary-100
                                      cursor-pointer">
                        <p class="text-xs text-muted mt-2">รองรับไฟล์ .xlsx, .xls, .csv (ไม่เกิน 2MB)</p>
                    </div>
                    @error('file')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Format Info -->
                <div class="bg-surface-50 rounded-xl p-4 text-sm">
                    <h4 class="font-semibold text-text mb-2 flex items-center gap-2 text-xs">
                        <i data-lucide="info" class="w-4 h-4 text-primary-400"></i>
                        รูปแบบไฟล์ที่รองรับ
                    </h4>
                    <ul class="text-muted space-y-1 text-xs">
                        <li><strong class="text-text">คอลัมน์ A:</strong> รหัสพนักงาน (จำเป็น)</li>
                        <li><strong class="text-text">คอลัมน์ B:</strong> ชื่อจริง (จำเป็น)</li>
                        <li><strong class="text-text">คอลัมน์ C:</strong> นามสกุล (จำเป็น)</li>
                        <li><strong class="text-text">คอลัมน์ D:</strong> แผนก</li>
                        <li><strong class="text-text">คอลัมน์ E:</strong> ตำแหน่ง</li>
                    </ul>
                    <p class="text-xs text-amber-600 mt-2 flex items-start gap-1">
                        <i data-lucide="alert-triangle" class="w-3 h-3 mt-0.5 flex-shrink-0"></i>
                        หากรหัสพนักงานซ้ำ ระบบจะอัพเดตข้อมูลพนักงานคนนั้นอัตโนมัติ
                    </p>
                </div>
                
                <!-- Download Template -->
                <a href="{{ route('employees.template') }}" class="flex items-center justify-center gap-2 text-sm text-primary-600 hover:text-primary-700 font-medium transition-colors duration-150 cursor-pointer">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    ดาวน์โหลด Template ตัวอย่าง
                </a>
                
                <!-- Actions -->
                <div class="flex gap-3 pt-2">
                    <button type="button" @click="showImportModal = false" class="btn-secondary flex-1">
                        ยกเลิก
                    </button>
                    <button type="submit" class="btn-primary flex-1">
                        <i data-lucide="upload" class="w-4 h-4"></i>
                        นำเข้าข้อมูล
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection



