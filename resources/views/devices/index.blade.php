@extends('layouts.app')

@section('title', 'จัดการอุปกรณ์')

@section('content')
<div class="space-y-6">
    <!-- Header & Action -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-text font-bold font-mono font-mono">อุปกรณ์ลงเวลา</h2>
            <p class="text-indigo-600/70 text-sm">จัดการจุดลงเวลาและอุปกรณ์ Kiosk ทั้งหมด</p>
        </div>
        <a href="{{ route('devices.create') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 shadow-md hover:shadow-lg transition-all text-white px-4 py-2 rounded-xl transition-all shadow-sm hover:shadow-md text-sm font-medium">
            <x-heroicon-o-plus class="w-5"/> ลงทะเบียนอุปกรณ์
        </a>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-700 px-4 py-3 rounded-xl border border-emerald-100 flex items-center gap-3 shadow-sm">
            <x-heroicon-o-check-circle class="text-lg w-5"/>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Data Table -->
    <div class="bg-card rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-text/80">
                <thead class="bg-slate-50/50 text-indigo-600/70 font-semibold border-b border-slate-200/60">
                    <tr>
                        <th class="px-6 py-4">ชื่ออุปกรณ์</th>
                        <th class="px-6 py-4">รหัสอุปกรณ์ (Device Code)</th>
                        <th class="px-6 py-4">สถานที่ติดตั้ง</th>
                        <th class="px-6 py-4">IP Address</th>
                        <th class="px-6 py-4 text-center">สถานะ</th>
                        <th class="px-6 py-4 text-right">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($devices as $device)
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg shadow-sm border border-indigo-100">
                                    <x-heroicon-o-device-tablet class="w-5"/>
                                </div>
                                <span class="font-bold text-text group-hover:text-primary-700 transition-colors">{{ $device->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-mono text-indigo-600/70 bg-slate-50 px-2 py-1 rounded text-xs border border-slate-200/60 select-all">{{ $device->device_code }}</span>
                        </td>
                        <td class="px-6 py-4 text-text/80">
                            <div class="flex items-center gap-2">
                                <x-heroicon-o-map-pin class="text-slate-300 w-5"/>
                                {{ $device->location ?? '-' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 font-mono text-xs text-indigo-600/70">{{ $device->ip_address ?? '-' }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($device->is_active)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-full text-xs font-bold border border-emerald-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> ออนไลน์
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 text-text/80 rounded-full text-xs font-bold border border-slate-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> ออฟไลน์
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('devices.edit', $device) }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-primary-400 hover:text-indigo-600 hover:bg-indigo-50/50 transition-all" title="แก้ไข">
                                    <x-heroicon-o-pencil-square class="w-5"/>
                                </a>
                                <form action="{{ route('devices.destroy', $device) }}" method="POST" onsubmit="return confirm('ยืนยันการลบอุปกรณ์นี้?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg text-primary-400 hover:text-rose-600 hover:bg-rose-50 transition-all" title="ลบ">
                                        <x-heroicon-o-trash class="w-5"/>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center text-primary-400">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <x-heroicon-o-device-tablet class="text-2xl text-slate-300 w-5"/>
                            </div>
                            <p class="font-medium">ไม่พบข้อมูลอุปกรณ์</p>
                            <p class="text-sm mt-1 text-primary-400">เริ่มต้นด้วยการลงทะเบียนอุปกรณ์ใหม่</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection