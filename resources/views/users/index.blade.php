@extends('layouts.app')

@section('title', 'จัดการผู้ใช้งาน')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="section-title">รายชื่อผู้ใช้งานระบบ</h2>
            <p class="section-subtitle">จัดการบัญชีผู้ดูแลระบบ (Admin) และเจ้าหน้าที่ (HR/Verifier/Approver)</p>
        </div>
        <a href="{{ route('users.create') }}" class="btn-primary">
            <i data-lucide="plus" class="w-4 h-4"></i> เพิ่มผู้ใช้งาน
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
                        <th class="table-cell">ชื่อ-นามสกุล</th>
                        <th class="table-cell">อีเมล</th>
                        <th class="table-cell">บทบาท (Role)</th>
                        <th class="table-cell">ลายเซ็น</th>
                        <th class="table-cell text-right">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary-50/60">
                    @foreach($users as $user)
                    <tr class="table-row group">
                        <td class="table-cell">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-primary-100 flex items-center justify-center text-primary-700 font-semibold text-sm uppercase">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <span class="font-semibold text-text text-sm group-hover:text-primary-700 transition-colors duration-150">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="table-cell text-muted text-sm">{{ $user->email }}</td>
                        <td class="table-cell">
                            <span class="badge-info capitalize">{{ $user->role }}</span>
                        </td>
                        <td class="table-cell">
                            @if($user->signature_path)
                                <span class="text-emerald-600 text-sm flex items-center gap-1.5">
                                    <i data-lucide="check-circle" class="w-4 h-4"></i> มีแล้ว
                                </span>
                            @else
                                <span class="text-muted text-sm">ยังไม่มี</span>
                            @endif
                        </td>
                        <td class="table-cell text-right">
                            <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-150">
                                <a href="{{ route('users.edit', $user) }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-muted hover:text-primary-600 hover:bg-primary-50 transition-colors duration-150 cursor-pointer" title="แก้ไข">
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </a>
                                @if(auth()->id() !== $user->id)
                                <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('ยืนยันการลบผู้ใช้งานนี้?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg text-muted hover:text-red-600 hover:bg-red-50 transition-colors duration-150 cursor-pointer" title="ลบ">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-primary-100/60 bg-surface-50/40">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection




