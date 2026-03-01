<!-- Export Options Modal -->
<div id="exportModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="document.getElementById('exportModal').classList.add('hidden')"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-card text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md">
                
                <div class="bg-card px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-rose-100 sm:mx-0 sm:h-10 sm:w-10">
                            <x-heroicon-o-pencil-square class="text-rose-600 w-5"/>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                            <h3 class="text-base font-semibold leading-6 text-slate-900 font-mono" id="modal-title">ตัวเลือกการพิมพ์รายงาน</h3>
                            <div class="mt-2">
                                <p class="text-sm text-indigo-600/70 mb-4">เลือกผู้ลงนามท้ายรายงาน (หากไม่เลือก จะเว้นว่างไว้)</p>
                                
                                <form action="{{ route('reports.pdf') }}" method="GET" target="_blank" class="space-y-4">
                                    <!-- Hidden Filters -->
                                    <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                                    <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                                    <input type="hidden" name="employee_id" value="{{ request('employee_id') }}">
                                    <input type="hidden" name="department" value="{{ request('department') }}">

                                    <!-- Verifier -->
                                    <div>
                                        <label class="block text-sm font-medium text-text">ผู้ตรวจสอบ (Verifier)</label>
                                        <select name="verifier_id" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 sm:text-sm">
                                            <option value="">-- ไม่ระบุ --</option>
                                            @foreach($users as $u)
                                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->role }})</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Approver -->
                                    <div>
                                        <label class="block text-sm font-medium text-text">ผู้อนุมัติ (Approver)</label>
                                        <select name="approver_id" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 sm:text-sm">
                                            <option value="">-- ไม่ระบุ --</option>
                                            @foreach($users as $u)
                                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->role }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="mt-6 flex justify-end gap-3">
                                        <button type="button" onclick="document.getElementById('exportModal').classList.add('hidden')" class="inline-flex justify-center rounded-lg bg-card px-3 py-2 text-sm font-semibold text-slate-900 ring-1 ring-inset ring-slate-300 hover:bg-slate-50 shadow-sm transition-all">ยกเลิก</button>
                                        <button type="submit" onclick="document.getElementById('exportModal').classList.add('hidden')" class="inline-flex justify-center rounded-lg bg-rose-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-rose-500 transition-all">
                                            <x-heroicon-o-printer class="mr-2 w-5"/> พิมพ์รายงาน
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
