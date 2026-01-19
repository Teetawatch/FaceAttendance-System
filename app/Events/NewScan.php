<?php

namespace App\Events;

use App\Models\AttendanceLog;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast; // สำคัญ!
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewScan implements ShouldBroadcast // ต้อง implement interface นี้
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $log;
    public $employee;

    /**
     * Create a new event instance.
     */
    public function __construct(AttendanceLog $log)
    {
        // Load employee data to send with event
        $this->log = $log->load(['employee', 'device']);
        
        // Prepare Thai Date
        $scanTime = \Carbon\Carbon::parse($log->scan_time);
        $thaiDate = $scanTime->copy()->addYears(543)->locale('th')->isoFormat('D MMM YYYY เวลา HH:mm');

        // Format ข้อมูลให้ Frontend ใช้ง่ายๆ
        $this->employee = [
            'name' => $log->employee->first_name . ' ' . $log->employee->last_name,
            'photo_url' => $log->employee->photo_path ? route('storage.file', ['path' => $log->employee->photo_path]) : null,
            'snapshot_url' => $log->snapshot_path ? route('storage.file', ['path' => $log->snapshot_path]) : null,
            'time' => $log->scan_time->format('H:i:s'),
            'datetime_th' => $thaiDate,
            'type' => strtoupper($log->scan_type), // IN / OUT
            'device' => $log->device->name,
            'status_color' => $log->scan_type === 'in' ? 'text-green-600' : 'text-orange-500',
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        // ส่งไปที่ Public Channel ชื่อ 'scans'
        return [
            new Channel('scans'),
        ];
    }
    
    /**
     * กำหนดชื่อ Event ที่จะส่งไป (Optional แต่แนะนำ)
     */
    public function broadcastAs()
    {
        return 'new-scan';
    }
}