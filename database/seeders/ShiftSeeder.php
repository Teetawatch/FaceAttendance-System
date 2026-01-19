<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shift;
use App\Models\Employee;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create/Update Default Shift (Late after 09:00)
        // User requested: Arrival 05:30 - 09:00. After 09:00 is late.
        // So start_time (late threshold) = 09:00:00.
        $defaultShift = Shift::updateOrCreate(
            ['name' => 'General Shift'],
            [
                'start_time' => '09:00:00',
                'end_time' => '18:00:00', // Dummy end time
            ]
        );

        // 2. Assign to all employees who don't have a shift
        Employee::whereNull('shift_id')->update(['shift_id' => $defaultShift->id]);
        
        $this->command->info('Default shift updated (Start: 09:00) and assigned.');
    }
}
