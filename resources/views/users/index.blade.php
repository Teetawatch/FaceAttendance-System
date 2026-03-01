@extends('layouts.app')

@section('title', 'จัดการผู้ใช้งาน')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center bg-card p-6 rounded-2xl shadow-sm border border-primary-50">
        <div>
            <h2 class="text-xl font-bold text-text font-bold font-mono font-mono">รายชื่อผู้ใช้งานระบบ</h2>
            <p class="text-primary-600/70 text-sm mt-1">จัดการบัญชีผู้ดูแลระบบ (Admin) และเจ้าหน้าที่ (HR/Verifier/Approver)</p>
        </div>
        <a href="{{ route('users.create') }}" class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white px-5 py-2.5 rounded-xl transition-all shadow-sm hover:shadow-md font-medium">
            <i class="fa-solid fa-user-plus"></i> เพิ่มผู้ใช้งาน
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-4 rounded-xl border border-green-100 flex items-center gap-3">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 text-red-700 p-4 rounded-xl border border-red-100 flex items-center gap-3">
            <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
        </div>
    @endif

    <!-- Users Table -->
    <div class="bg-card rounded-2xl shadow-sm border border-primary-50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-background border-b border-primary-50 text-primary-600/70 text-sm uppercase tracking-wider">
                        <th class="px-6 py-4 font-semibold">ชื่อ-นามสกุล</th>
                        <th class="px-6 py-4 font-semibold">อีเมล</th>
                        <th class="px-6 py-4 font-semibold">บทบาท (Role)</th>
                        <th class="px-6 py-4 font-semibold">ลายเซ็น</th>
                        <th class="px-6 py-4 font-semibold text-right">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($users as $user)
                    <tr class="hover:bg-background transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-text/80 font-bold border border-primary-100">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <span class="font-medium text-text font-bold font-mono">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-text/80">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 capitalize">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($user->signature_path)
                                <span class="text-green-600 text-sm flex items-center gap-1"><i class="fa-solid fa-check"></i> มีแล้ว</span>
                            @else
                                <span class="text-primary-400 text-sm">ยังไม่มี</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('users.edit', $user) }}" class="p-2 text-primary-400 hover:text-amber-500 hover:bg-amber-50 rounded-lg transition-colors" title="แก้ไข">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                @if(auth()->id() !== $user->id)
                                <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('ยืนยันการลบผู้ใช้งานนี้?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-primary-400 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-colors" title="ลบ">
                                        <i class="fa-regular fa-trash-can"></i>
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
        <div class="p-4 border-t border-primary-50">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
