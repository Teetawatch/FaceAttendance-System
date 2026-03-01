@extends('layouts.app')

@section('title', 'รายงานการลงเวลา')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="section-title">รายงานการลงเวลา</h2>
            <p class="section-subtitle">ดูข้อมูลสรุปและส่งออกรายงานเป็นไฟล์ Excel</p>
        </div>
        <div class="flex gap-2">
            <!-- PDF Export Form -->
            <form action="{{ route('reports.pdf') }}" method="GET" target="_blank" x-data="{ verifier: '', approver: '' }">
                <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                <input type="hidden" name="employee_id" value="{{ request('employee_id') }}">
                <input type="hidden" name="department" value="{{ request('department') }}">
                <input type="hidden" name="verifier_id" x-model="verifier">
                <input type="hidden" name="approver_id" x-model="approver">
                
                <button type="button" onclick="document.getElementById('exportModal').classList.remove('hidden')" class="btn-danger">
                    <i data-lucide="file-text" class="w-4 h-4"></i> Export PDF
                </button>
            </form>

            <!-- Excel Export Form -->
            <form action="{{ route('reports.export') }}" method="GET" target="_blank">
                <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                <input type="hidden" name="employee_id" value="{{ request('employee_id') }}">
                <input type="hidden" name="department" value="{{ request('department') }}">
                
                <button type="submit" class="btn-accent">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4"></i> Export Excel
                </button>
            </form>
        </div>
    </div>

    <!-- Filters -->
    <div class="card p-5">
        <form action="{{ route('reports.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            
            <div>
                <label class="block text-xs font-medium text-text mb-1.5">ตั้งแต่วันที่</label>
                <input type="date" name="start_date" value="{{ request('start_date', \Carbon\Carbon::now()->format('Y-m-d')) }}" 
                       class="w-full bg-white border border-primary-200 rounded-xl text-sm px-3 py-2.5 focus:ring-2 focus:ring-primary-100 focus:border-primary-400 transition-colors duration-150">
            </div>
            <div>
                <label class="block text-xs font-medium text-text mb-1.5">ถึงวันที่</label>
                <input type="date" name="end_date" value="{{ request('end_date', \Carbon\Carbon::now()->format('Y-m-d')) }}" 
                       class="w-full bg-white border border-primary-200 rounded-xl text-sm px-3 py-2.5 focus:ring-2 focus:ring-primary-100 focus:border-primary-400 transition-colors duration-150">
            </div>

            <div>
                <label class="block text-xs font-medium text-text mb-1.5">ค้นหา</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ชื่อ หรือ รหัสพนักงาน..."
                       class="w-full bg-white border border-primary-200 rounded-xl text-sm px-3 py-2.5 focus:ring-2 focus:ring-primary-100 focus:border-primary-400 transition-colors duration-150">
            </div>

            <div>
                <label class="block text-xs font-medium text-text mb-1.5">พนักงาน</label>
                <select name="employee_id" class="w-full bg-white border border-primary-200 rounded-xl text-sm px-3 py-2.5 focus:ring-2 focus:ring-primary-100 focus:border-primary-400 transition-colors duration-150">
                    <option value="">ทั้งหมด</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                            {{ $emp->first_name }} {{ $emp->last_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-text mb-1.5">แผนก</label>
                <select name="department" class="w-full bg-white border border-primary-200 rounded-xl text-sm px-3 py-2.5 focus:ring-2 focus:ring-primary-100 focus:border-primary-400 transition-colors duration-150">
                    <option value="">ทั้งหมด</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>
                            {{ $dept }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-4 flex justify-end mt-1">
                <button type="submit" class="btn-primary">
                    <i data-lucide="filter" class="w-4 h-4"></i> กรองข้อมูล
                </button>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="table-header">
                    <tr>
                        <th class="table-cell">วันที่</th>
                        <th class="table-cell">พนักงาน</th>
                        <th class="table-cell">เข้างาน</th>
                        <th class="table-cell">ออกงาน</th>
                        <th class="table-cell">รวมเวลา</th>
                        <th class="table-cell text-center">สถานะ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary-50/60">
                    @forelse($attendances as $row)
                    <tr class="table-row group">
                        <td class="table-cell font-mono text-xs text-muted">
                            {{ \Carbon\Carbon::parse($row->date)->locale('th')->isoFormat('D MMM YY') }}
                        </td>
                        <td class="table-cell">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-surface-50 overflow-hidden flex-shrink-0 border border-primary-100/60">
                                    @if($row->employee->photo_path)
                                        <img src="{{ route('storage.file', ['path' => $row->employee->photo_path]) }}" class="w-full h-full object-cover" alt="{{ $row->employee->first_name }}">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-muted">
                                            <i data-lucide="user" class="w-3.5 h-3.5"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-semibold text-text text-sm group-hover:text-primary-700 transition-colors duration-150">{{ $row->employee->first_name }} {{ $row->employee->last_name }}</p>
                                    <p class="text-xs text-muted font-mono">{{ $row->employee->employee_code }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="table-cell">
                            @if($row->check_in_at)
                                <span class="badge-success font-mono text-xs">{{ $row->check_in_at->format('H:i') }}</span>
                            @else
                                <span class="text-muted/40">-</span>
                            @endif
                        </td>
                        <td class="table-cell">
                            @if($row->check_out_at)
                                <span class="badge-warning font-mono text-xs">{{ $row->check_out_at->format('H:i') }}</span>
                            @else
                                <span class="text-muted/40">-</span>
                            @endif
                        </td>
                        <td class="table-cell font-mono text-xs text-muted">
                            {{ $row->total_work_minutes ? floor($row->total_work_minutes / 60) . ' ชม. ' . ($row->total_work_minutes % 60) . ' นาที' : '-' }}
                        </td>
                        <td class="table-cell text-center">
                            @if($row->status == 'present')
                                <span class="badge-success">ปกติ</span>
                            @elseif($row->status == 'late')
                                <span class="badge-warning">สาย ({{ $row->late_minutes }} น.)</span>
                            @elseif($row->status == 'absent')
                                <span class="badge-danger">ขาดงาน</span>
                            @elseif($row->status == 'leave')
                                <span class="badge-info">ลางาน</span>
                            @else
                                <span class="badge-neutral">-</span>
                            @endif
                        </td>
                        <td class="table-cell text-right">
                           <button onclick='openEditModal(@json($row))' 
                                   class="w-8 h-8 flex items-center justify-center rounded-lg text-muted hover:text-primary-600 hover:bg-primary-50 transition-colors duration-150 cursor-pointer">
                               <i data-lucide="pencil" class="w-4 h-4"></i>
                           </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-16 text-center">
                            <div class="w-14 h-14 bg-surface-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                <i data-lucide="bar-chart-3" class="w-6 h-6 text-muted"></i>
                            </div>
                            <p class="font-medium text-text text-sm">ไม่พบข้อมูลการลงเวลา</p>
                            <p class="text-xs mt-1 text-muted">ลองปรับเปลี่ยนเงื่อนไขการค้นหา</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-5 py-4 border-t border-primary-100/60 bg-surface-50/40">
            {{ $attendances->links() }}
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-primary-950/30 backdrop-blur-sm transition-opacity" onclick="closeEditModal()"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
      <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-2xl bg-white border border-primary-100/60 text-left shadow-lg transition-all sm:my-8 sm:w-full sm:max-w-lg">
          
          <!-- Header -->
          <div class="px-5 py-4 border-b border-primary-100/60 flex justify-between items-center">
            <h3 class="text-base font-semibold text-text" id="modal-title">แก้ไขข้อมูลการลงเวลา</h3>
            <button type="button" onclick="closeEditModal()" class="text-muted hover:text-text transition-colors duration-150 cursor-pointer p-1 rounded-lg hover:bg-surface-50">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
          </div>

          <!-- Form -->
          <form id="editForm" method="POST">
              @csrf
              <input type="hidden" name="_method" id="formMethod" value="PATCH">
              <input type="hidden" name="employee_id" id="editEmployeeId">
              <input type="hidden" name="date" id="editDate">

              <div class="p-5 space-y-4">
                  <!-- Employee Info -->
                  <div class="flex items-center gap-3 p-3 bg-surface-50 rounded-xl border border-primary-100/60">
                      <div class="w-10 h-10 rounded-xl bg-primary-100 flex items-center justify-center text-primary-600">
                          <i data-lucide="user" class="w-5 h-5"></i>
                      </div>
                      <div>
                          <p class="font-semibold text-text" id="modalEmployeeName">Loading...</p>
                          <p class="text-xs text-muted" id="modalDateDisplay">Loading...</p>
                      </div>
                  </div>

                  <!-- Status -->
                  <div>
                      <label for="status" class="block text-sm font-medium text-text mb-1.5">สถานะ</label>
                      <select id="status" name="status" class="w-full rounded-xl border-primary-200 text-sm py-2.5 focus:ring-2 focus:ring-primary-100 focus:border-primary-400 transition-colors duration-150">
                        <option value="present">ปกติ (Present)</option>
                        <option value="late">สาย (Late)</option>
                        <option value="absent">ขาดงาน (Absent)</option>
                        <option value="leave">ลางาน (Leave)</option>
                        <option value="missing">ไม่ลงชื่อ (Missing)</option>
                      </select>
                  </div>

                  <!-- Remarks -->
                  <div>
                      <label for="remarks" class="block text-sm font-medium text-text mb-1.5">หมายเหตุ</label>
                      <textarea id="remarks" name="remarks" rows="3" class="w-full rounded-xl border-primary-200 text-sm py-2.5 focus:ring-2 focus:ring-primary-100 focus:border-primary-400 placeholder:text-muted/50 transition-colors duration-150" placeholder="ระบุสาเหตุการลาป่วย, ลากิจ หรือเหตุผลอื่นๆ..."></textarea>
                  </div>
              </div>

              <!-- Footer -->
              <div class="px-5 py-4 border-t border-primary-50 flex flex-row-reverse gap-2">
                <button type="submit" class="btn-primary">บันทึกข้อมูล</button>
                <button type="button" onclick="closeEditModal()" class="btn-secondary">ยกเลิก</button>
              </div>
          </form>
        </div>
      </div>
    </div>
</div>

<script>
    function openEditModal(data) {
        const modal = document.getElementById('editModal');
        const form = document.getElementById('editForm');
        const methodInput = document.getElementById('formMethod');
        
        document.getElementById('editEmployeeId').value = data.employee.id;
        document.getElementById('editDate').value = data.date;
        document.getElementById('modalEmployeeName').innerText = data.employee.first_name + ' ' + data.employee.last_name;
        
        const dateObj = new Date(data.date);
        document.getElementById('modalDateDisplay').innerText = dateObj.toLocaleDateString('th-TH', { year: 'numeric', month: 'long', day: 'numeric' });

        document.getElementById('status').value = data.status;
        document.getElementById('remarks').value = data.remarks || '';

        if (data.id) {
            form.action = `/attendance/${data.id}`;
            methodInput.disabled = false;
        } else {
            form.action = `/attendance`;
            methodInput.disabled = true;
        }

        modal.classList.remove('hidden');
    }

    function closeEditModal() {
        const modal = document.getElementById('editModal');
        modal.classList.add('hidden');
    }
</script>
@include('reports.partials.export-modal')
@endsection




