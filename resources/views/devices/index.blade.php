@extends('layouts.app')

@section('title', 'จัดการอุปกรณ์')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="section-title">อุปกรณ์ลงเวลา</h2>
            <p class="section-subtitle">จัดการจุดลงเวลาและอุปกรณ์ Kiosk ทั้งหมด</p>
        </div>
        <a href="{{ route('devices.create') }}" class="btn-primary">
            <i data-lucide="plus" class="w-4 h-4"></i> ลงทะเบียนอุปกรณ์
        </a>
    </div>

    @if(session('success'))
        <div class="alert-success">
            <i data-lucide="check-circle" class="w-4 h-4 flex-shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="table-header">
                    <tr>
                        <th class="table-cell">ชื่ออุปกรณ์</th>
                        <th class="table-cell">รหัสอุปกรณ์ (Device Code)</th>
                        <th class="table-cell">สถานที่ติดตั้ง</th>
                        <th class="table-cell">IP Address</th>
                        <th class="table-cell text-center">สถานะ</th>
                        <th class="table-cell text-right">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary-50/60">
                    @forelse($devices as $device)
                    <tr class="table-row group">
                        <td class="table-cell">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center border border-primary-100">
                                    <i data-lucide="tablet-smartphone" class="w-4 h-4"></i>
                                </div>
                                <span class="font-semibold text-text text-sm group-hover:text-primary-700 transition-colors duration-150">{{ $device->name }}</span>
                            </div>
                        </td>
                        <td class="table-cell">
                            <span class="font-mono text-primary-600 bg-primary-50 px-2 py-1 rounded-lg text-xs select-all">{{ $device->device_code }}</span>
                        </td>
                        <td class="table-cell text-muted">
                            <div class="flex items-center gap-1.5">
                                <i data-lucide="map-pin" class="w-3.5 h-3.5"></i>
                                {{ $device->location ?? '-' }}
                            </div>
                        </td>
                        <td class="table-cell font-mono text-xs text-muted">{{ $device->ip_address ?? '-' }}</td>
                        <td class="table-cell text-center">
                            @if($device->is_active)
                                <span class="badge-success">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> ออนไลน์
                                </span>
                            @else
                                <span class="badge-neutral">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> ออฟไลน์
                                </span>
                            @endif
                        </td>
                        <td class="table-cell text-right">
                            <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-150">
                                <a href="{{ route('devices.edit', $device) }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-muted hover:text-primary-600 hover:bg-primary-50 transition-colors duration-150 cursor-pointer" title="แก้ไข">
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </a>
                                <form action="{{ route('devices.destroy', $device) }}" method="POST" onsubmit="return confirm('ยืนยันการลบอุปกรณ์นี้?');">
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
                                <i data-lucide="tablet-smartphone" class="w-6 h-6 text-muted"></i>
                            </div>
                            <p class="font-medium text-text text-sm">ไม่พบข้อมูลอุปกรณ์</p>
                            <p class="text-xs mt-1 text-muted">เริ่มต้นด้วยการลงทะเบียนอุปกรณ์ใหม่</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection



