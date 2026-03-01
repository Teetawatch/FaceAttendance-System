<!-- Export Options Modal -->
<div id="exportModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-primary-950/30 backdrop-blur-sm transition-opacity" onclick="document.getElementById('exportModal').classList.add('hidden')"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white border border-primary-100/60 text-left shadow-lg transition-all sm:my-8 sm:w-full sm:max-w-md">
                
                <div class="px-5 py-4 border-b border-primary-100/60 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-red-50 text-red-600 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-text" id="modal-title">ตัวเลือกการพิมพ์รายงาน</h3>
                        <p class="text-xs text-muted">เลือกผู้ลงนามท้ายรายงาน (หากไม่เลือก จะเว้นว่างไว้)</p>
                    </div>
                </div>

                <form action="{{ route('reports.pdf') }}" method="GET" target="_blank" class="p-5 space-y-4">
                    <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                    <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                    <input type="hidden" name="employee_id" value="{{ request('employee_id') }}">
                    <input type="hidden" name="department" value="{{ request('department') }}">

                    <div>
                        <label class="block text-sm font-medium text-text mb-1.5">ผู้ตรวจสอบ (Verifier)</label>
                        <select name="verifier_id" class="input-field">
                            <option value="">-- ไม่ระบุ --</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->role }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-text mb-1.5">ผู้อนุมัติ (Approver)</label>
                        <select name="approver_id" class="input-field">
                            <option value="">-- ไม่ระบุ --</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->role }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="pt-4 border-t border-primary-50 flex justify-end gap-3">
                        <button type="button" onclick="document.getElementById('exportModal').classList.add('hidden')" class="btn-secondary">ยกเลิก</button>
                        <button type="submit" onclick="document.getElementById('exportModal').classList.add('hidden')" class="btn-danger">
                            <i data-lucide="printer" class="w-4 h-4"></i> พิมพ์รายงาน
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>




