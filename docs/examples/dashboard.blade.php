{{-- 
    ตัวอย่าง Blade View สำหรับแสดง Dashboard รายงานการเข้างาน
    ในเว็บไซต์ Laravel อื่น
    
    ใช้ JavaScript ดึงข้อมูลแบบ Real-time ทุก 30 วินาที
--}}

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard การเข้างาน</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8" x-data="attendanceDashboard()">
        <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center">
            📊 Dashboard การเข้างาน (Real-time)
        </h1>

        <!-- เวลาอัพเดทล่าสุด -->
        <div class="text-center mb-6">
            <span class="text-sm text-gray-500">
                อัพเดทล่าสุด: <span x-text="lastUpdated" class="font-semibold"></span>
                <span class="ml-2 inline-flex items-center">
                    <span class="animate-pulse w-2 h-2 bg-green-500 rounded-full mr-1"></span>
                    Live
                </span>
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- ส่วนข้าราชการ -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-blue-600 mb-4 flex items-center">
                    <span class="mr-2">👔</span> ข้าราชการ
                </h2>

                <!-- สรุปตัวเลข -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-blue-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-blue-600" x-text="staff.total_employees">-</div>
                        <div class="text-sm text-gray-600">ข้าราชการทั้งหมด</div>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-green-600" x-text="staff.on_time">-</div>
                        <div class="text-sm text-gray-600">เข้าตรงเวลา</div>
                    </div>
                    <div class="bg-yellow-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-yellow-600" x-text="staff.late">-</div>
                        <div class="text-sm text-gray-600">เข้าสาย</div>
                    </div>
                    <div class="bg-red-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-red-600" x-text="staff.absent">-</div>
                        <div class="text-sm text-gray-600">ยังไม่เข้า</div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mb-4">
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                        <span>อัตราการเข้างาน</span>
                        <span x-text="staff.attendance_rate + '%'"></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-gradient-to-r from-blue-500 to-green-500 h-3 rounded-full transition-all duration-500"
                             :style="'width: ' + staff.attendance_rate + '%'"></div>
                    </div>
                </div>

                <!-- รายการล่าสุด -->
                <h3 class="font-semibold text-gray-700 mb-2">🔔 สแกนล่าสุด</h3>
                <div class="space-y-2 max-h-48 overflow-y-auto">
                    <template x-for="scan in staffRecentScans" :key="scan.scan_time">
                        <div class="flex items-center justify-between bg-gray-50 rounded-lg p-2 text-sm">
                            <div>
                                <span class="font-medium" x-text="scan.full_name"></span>
                                <span class="text-gray-500 text-xs ml-2" x-text="scan.department"></span>
                            </div>
                            <div class="flex items-center">
                                <span :class="scan.scan_type === 'in' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700'"
                                      class="px-2 py-1 rounded text-xs mr-2"
                                      x-text="scan.scan_type === 'in' ? 'เข้า' : 'ออก'"></span>
                                <span class="text-gray-600" x-text="scan.scan_time"></span>
                                <span x-show="scan.is_late" class="ml-1 text-red-500">⚠️</span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- ส่วนนักเรียน -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-purple-600 mb-4 flex items-center">
                    <span class="mr-2">🎓</span> นักเรียน
                </h2>

                <!-- สรุปตัวเลข -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-purple-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-purple-600" x-text="student.total_students">-</div>
                        <div class="text-sm text-gray-600">นักเรียนทั้งหมด</div>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-green-600" x-text="student.on_time">-</div>
                        <div class="text-sm text-gray-600">เข้าตรงเวลา</div>
                    </div>
                    <div class="bg-yellow-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-yellow-600" x-text="student.late">-</div>
                        <div class="text-sm text-gray-600">เข้าสาย</div>
                    </div>
                    <div class="bg-red-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-red-600" x-text="student.absent">-</div>
                        <div class="text-sm text-gray-600">ยังไม่เข้า</div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mb-4">
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                        <span>อัตราการเข้าเรียน</span>
                        <span x-text="student.attendance_rate + '%'"></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-gradient-to-r from-purple-500 to-pink-500 h-3 rounded-full transition-all duration-500"
                             :style="'width: ' + student.attendance_rate + '%'"></div>
                    </div>
                </div>

                <!-- รายการล่าสุด -->
                <h3 class="font-semibold text-gray-700 mb-2">🔔 สแกนล่าสุด</h3>
                <div class="space-y-2 max-h-48 overflow-y-auto">
                    <template x-for="scan in studentRecentScans" :key="scan.scan_time">
                        <div class="flex items-center justify-between bg-gray-50 rounded-lg p-2 text-sm">
                            <div>
                                <span class="font-medium" x-text="scan.full_name"></span>
                                <span class="text-gray-500 text-xs ml-2" x-text="scan.course_name"></span>
                            </div>
                            <div class="flex items-center">
                                <span :class="scan.period === 'morning' ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700'"
                                      class="px-2 py-1 rounded text-xs mr-2"
                                      x-text="scan.period === 'morning' ? 'เช้า' : 'บ่าย'"></span>
                                <span class="text-gray-600" x-text="scan.scan_time"></span>
                                <span x-show="scan.is_late" class="ml-1 text-red-500">⚠️</span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- ข้อความสถานะ -->
        <div class="mt-8 text-center">
            <p class="text-sm text-gray-500">
                ข้อมูลจะอัพเดทอัตโนมัติทุก 30 วินาที
            </p>
        </div>
    </div>

    <script>
        function attendanceDashboard() {
            return {
                staff: {
                    total_employees: 0,
                    checked_in: 0,
                    on_time: 0,
                    late: 0,
                    absent: 0,
                    attendance_rate: 0
                },
                student: {
                    total_students: 0,
                    checked_in: 0,
                    on_time: 0,
                    late: 0,
                    absent: 0,
                    attendance_rate: 0
                },
                staffRecentScans: [],
                studentRecentScans: [],
                lastUpdated: '-',

                init() {
                    this.fetchData();
                    // อัพเดททุก 30 วินาที
                    setInterval(() => this.fetchData(), 30000);
                },

                async fetchData() {
                    try {
                        // ดึงข้อมูลข้าราชการ
                        const staffRes = await fetch('/api/attendance/staff-summary');
                        if (staffRes.ok) {
                            const staffData = await staffRes.json();
                            if (staffData.success) {
                                this.staff = staffData.summary;
                                this.staffRecentScans = staffData.recent_scans || [];
                            }
                        }

                        // ดึงข้อมูลนักเรียน
                        const studentRes = await fetch('/api/attendance/student-summary');
                        if (studentRes.ok) {
                            const studentData = await studentRes.json();
                            if (studentData.success) {
                                this.student = studentData.summary;
                                this.studentRecentScans = studentData.recent_scans || [];
                            }
                        }

                        this.lastUpdated = new Date().toLocaleString('th-TH');
                    } catch (error) {
                        console.error('Error fetching attendance data:', error);
                    }
                }
            }
        }
    </script>
</body>
</html>
