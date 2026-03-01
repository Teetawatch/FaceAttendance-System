@extends('layouts.app')

@section('title', 'รายงานการลงเวลา')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-text font-bold font-mono font-mono">รายงานการลงเวลา</h2>
            <p class="text-indigo-600/70 text-sm">ดูข้อมูลสรุปและส่งออกรายงานเป็นไฟล์ Excel</p>
        </div>
        <div class="flex gap-2">
            <!-- PDF Export Form -->
            <form action="{{ route('reports.pdf') }}" method="GET" target="_blank" x-data="{ verifier: '', approver: '' }">
                <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                <input type="hidden" name="employee_id" value="{{ request('employee_id') }}">
                <input type="hidden" name="department" value="{{ request('department') }}">
                
                <!-- Signers (Dynamic from selection below) -->
                <input type="hidden" name="verifier_id" x-model="verifier">
                <input type="hidden" name="approver_id" x-model="approver">
                
                <div class="flex gap-2">
                    <!-- Helpers to select signer (Hidden visually, triggered by button or simplified UI) -->
                    <!-- For simplicity, we might add a 'Settings' modal later. For now, let's just create a small dropdown or assume defaults? 
                         Actually, let's keep it simple: Add a 'Configure Signers' button or just separate inputs visible?
                         Let's put the signer selection directly in the filter area or a separate modal. 
                         
                         Let's REVISE: Just put specific inputs in the filter area and copy them? 
                         OR better: Add a 'Print Options' button that opens a modal with signer selection + Export button.
                    -->
                    <button type="button" onclick="document.getElementById('exportModal').classList.remove('hidden')" 
                            class="inline-flex items-center gap-2 bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded-xl transition-all shadow-sm hover:shadow-md text-sm font-medium">
                        <x-heroicon-o-document-text class="w-5"/> Export PDF
                    </button>
                </div>
            </form>

            <!-- Excel Export Form -->
            <form action="{{ route('reports.export') }}" method="GET" target="_blank">
                <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                <input type="hidden" name="employee_id" value="{{ request('employee_id') }}">
                <input type="hidden" name="department" value="{{ request('department') }}">
                
                <button type="submit" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl transition-all shadow-sm hover:shadow-md text-sm font-medium">
                    <x-heroicon-o-table-cells class="w-5"/> Export Excel
                </button>
            </form>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-card rounded-2xl shadow-sm border border-slate-200/60 p-6">
        <form action="{{ route('reports.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            
            <!-- Date Range -->
            <div>
                <label class="block text-xs font-medium text-text mb-1">ตั้งแต่วันที่</label>
                <input type="date" name="start_date" value="{{ request('start_date', \Carbon\Carbon::now()->format('Y-m-d')) }}" 
                       class="w-full bg-slate-50 border border-slate-200 rounded-lg text-sm px-3 py-2 focus:ring-primary-500 focus:border-slate-200/600">
            </div>
            <div>
                <label class="block text-xs font-medium text-text mb-1">ถึงวันที่</label>
                <input type="date" name="end_date" value="{{ request('end_date', \Carbon\Carbon::now()->format('Y-m-d')) }}" 
                       class="w-full bg-slate-50 border border-slate-200 rounded-lg text-sm px-3 py-2 focus:ring-primary-500 focus:border-slate-200/600">
            </div>

            <!-- Search -->
            <div>
                <label class="block text-xs font-medium text-text mb-1">ค้นหา</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ชื่อ หรือ รหัสพนักงาน..."
                       class="w-full bg-slate-50 border border-slate-200 rounded-lg text-sm px-3 py-2 focus:ring-primary-500 focus:border-slate-200/600">
            </div>

            <!-- Employee -->
            <div>
                <label class="block text-xs font-medium text-text mb-1">พนักงาน</label>
                <select name="employee_id" class="w-full bg-slate-50 border border-slate-200 rounded-lg text-sm px-3 py-2 focus:ring-primary-500 focus:border-slate-200/600">
                    <option value="">ทั้งหมด</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                            {{ $emp->first_name }} {{ $emp->last_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Department -->
            <div>
                <label class="block text-xs font-medium text-text mb-1">แผนก</label>
                <select name="department" class="w-full bg-slate-50 border border-slate-200 rounded-lg text-sm px-3 py-2 focus:ring-primary-500 focus:border-slate-200/600">
                    <option value="">ทั้งหมด</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>
                            {{ $dept }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Submit -->
            <div class="md:col-span-4 flex justify-end mt-2">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 shadow-md hover:shadow-lg transition-all text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors">
                    <x-heroicon-o-funnel class="mr-1 w-5"/> กรองข้อมูล
                </button>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-card rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-text/80">
                <thead class="bg-slate-50/50 text-indigo-600/70 font-semibold border-b border-slate-200/60">
                    <tr>
                        <th class="px-6 py-4">วันที่</th>
                        <th class="px-6 py-4">พนักงาน</th>
                        <th class="px-6 py-4">เข้างาน</th>
                        <th class="px-6 py-4">ออกงาน</th>
                        <th class="px-6 py-4">รวมเวลา</th>
                        <th class="px-6 py-4 text-center">สถานะ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($attendances as $row)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-6 py-4 font-mono text-indigo-600/70">
                            {{ \Carbon\Carbon::parse($row->date)->locale('th')->isoFormat('D MMM YY') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-100 overflow-hidden flex-shrink-0 border border-slate-200">
                                    @if($row->employee->photo_path)
                                        <img src="{{ route('storage.file', ['path' => $row->employee->photo_path]) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-300">
                                            <x-heroicon-o-user class="text-xs w-5"/>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-bold text-text">{{ $row->employee->first_name }} {{ $row->employee->last_name }}</p>
                                    <p class="text-xs text-primary-400">{{ $row->employee->employee_code }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-mono text-green-600 font-medium">
                            {{ $row->check_in_at ? $row->check_in_at->format('H:i') : '-' }}
                        </td>
                        <td class="px-6 py-4 font-mono text-orange-500 font-medium">
                            {{ $row->check_out_at ? $row->check_out_at->format('H:i') : '-' }}
                        </td>
                        <td class="px-6 py-4 font-mono text-indigo-600/70">
                            {{ $row->total_work_minutes ? floor($row->total_work_minutes / 60) . ' ชม. ' . ($row->total_work_minutes % 60) . ' นาที' : '-' }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($row->status == 'present')
                                <span class="inline-flex items-center px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-full text-xs font-bold border border-emerald-100">ปกติ</span>
                            @elseif($row->status == 'late')
                                <span class="inline-flex items-center px-2.5 py-1 bg-amber-50 text-amber-700 rounded-full text-xs font-bold border border-amber-100">สาย ({{ $row->late_minutes }} น.)</span>
                            @elseif($row->status == 'absent')
                                <span class="inline-flex items-center px-2.5 py-1 bg-rose-50 text-rose-700 rounded-full text-xs font-bold border border-rose-100">ขาดงาน</span>
                            @elseif($row->status == 'leave')
                                <span class="inline-flex items-center px-2.5 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-bold border border-blue-100">ลางาน</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 bg-slate-100 text-text/80 rounded-full text-xs font-bold border border-slate-200">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                           <button onclick='openEditModal(@json($row))' 
                                   class="text-primary-400 hover:text-blue-600 transition-colors p-2 rounded-full hover:bg-blue-50">
                               <x-heroicon-o-pencil-square class="w-5"/>
                           </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center text-primary-400">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <x-heroicon-o-document-minus class="text-2xl text-slate-300 w-5"/>
                            </div>
                            <p class="font-medium">ไม่พบข้อมูลการลงเวลา</p>
                            <p class="text-sm mt-1 text-primary-400">ลองปรับเปลี่ยนเงื่อนไขการค้นหา</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-slate-200/60 bg-slate-50/50">
            {{ $attendances->links() }}
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="closeEditModal()"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
      <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-2xl bg-card text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
          
          <!-- Header -->
          <div class="bg-slate-50 px-4 py-3 sm:px-6 border-b border-slate-200/60 flex justify-between items-center">
            <h3 class="text-base font-semibold leading-6 text-slate-900 font-mono" id="modal-title">แก้ไขข้อมูลการลงเวลา</h3>
            <button type="button" onclick="closeEditModal()" class="text-primary-400 hover:text-indigo-600/70 transition-colors">
                <x-heroicon-o-x-mark class="text-lg w-5"/>
            </button>
          </div>

          <!-- Form -->
          <form id="editForm" method="POST">
              @csrf
              <!-- Method spoofing for PATCH will be added/removed via JS -->
              <input type="hidden" name="_method" id="formMethod" value="PATCH">
              
              <input type="hidden" name="employee_id" id="editEmployeeId">
              <input type="hidden" name="date" id="editDate">

              <div class="px-4 py-5 sm:p-6 space-y-4">
                  <!-- Employee Info -->
                  <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200/60">
                      <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center text-primary-400">
                          <x-heroicon-o-user class="w-5"/>
                      </div>
                      <div>
                          <p class="font-bold text-text" id="modalEmployeeName">Loading...</p>
                          <p class="text-xs text-indigo-600/70" id="modalDateDisplay">Loading...</p>
                      </div>
                  </div>

                  <!-- Status -->
                  <div>
                      <label for="status" class="block text-sm font-medium leading-6 text-slate-900">สถานะ</label>
                      <select id="status" name="status" class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-slate-900 ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-blue-600 sm:text-sm sm:leading-6">
                        <option value="present">ปกติ (Present)</option>
                        <option value="late">สาย (Late)</option>
                        <option value="absent">ขาดงาน (Absent)</option>
                        <option value="leave">ลางาน (Leave)</option>
                        <option value="missing">ไม่ลงชื่อ (Missing)</option>
                      </select>
                  </div>

                  <!-- Remarks -->
                  <div>
                      <label for="remarks" class="block text-sm font-medium leading-6 text-slate-900">หมายเหตุ</label>
                      <div class="mt-2">
                        <textarea id="remarks" name="remarks" rows="3" class="block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-primary-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6" placeholder="ระบุสาเหตุการลาป่วย, ลากิจ หรือเหตุผลอื่นๆ..."></textarea>
                      </div>
                  </div>
              </div>

              <!-- Footer -->
              <div class="bg-slate-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2">
                <button type="submit" class="inline-flex w-full justify-center rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:ml-3 sm:w-auto transition-all">บันทึกข้อมูล</button>
                <button type="button" onclick="closeEditModal()" class="mt-3 inline-flex w-full justify-center rounded-lg bg-card px-3 py-2 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 sm:mt-0 sm:w-auto transition-all">ยกเลิก</button>
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
        
        // Populate Data
        document.getElementById('editEmployeeId').value = data.employee.id;
        document.getElementById('editDate').value = data.date;
        document.getElementById('modalEmployeeName').innerText = data.employee.first_name + ' ' + data.employee.last_name;
        
        // Format Date
        const dateObj = new Date(data.date);
        document.getElementById('modalDateDisplay').innerText = dateObj.toLocaleDateString('th-TH', { year: 'numeric', month: 'long', day: 'numeric' });

        document.getElementById('status').value = data.status;
        document.getElementById('remarks').value = data.remarks || '';

        // Determine Action (Update vs Create)
        if (data.id) {
            // Update Existing Record
            form.action = `/attendance/${data.id}`;
            methodInput.disabled = false; // Enable _method=PATCH
        } else {
            // Create Prior Record
            form.action = `/attendance`;
            methodInput.disabled = true; // Disable _method to force POST
        }

        // Show Modal
        modal.classList.remove('hidden');
    }

    function closeEditModal() {
        const modal = document.getElementById('editModal');
        modal.classList.add('hidden');
    }
</script>
</script>
@include('reports.partials.export-modal')
@endsection
