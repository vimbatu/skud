<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\Employee;
use App\Services\AttendanceService;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $service = new AttendanceService();

        foreach (Employee::all() as $emp) {
            // последние 5 рабочих дней
            for ($i = 0; $i < 5; $i++) {
                $date = Carbon::now()->subDays($i)->format('Y-m-d');

                $timeIn = ($i % 2 === 0) ? '09:15:00' : '10:30:00';  // иногда опоздание
                $timeOut = ($i % 3 === 0) ? '17:45:00' : '18:10:00'; // иногда раньше уходит
                $worked = $service->calculateWorkedHours($timeIn, $timeOut);
                $deviation = $service->determineDeviation($timeIn, $timeOut, $worked);

                Attendance::updateOrCreate(
                    ['employee_id' => $emp->id, 'date' => $date],
                    [
                        'time_in' => $timeIn,
                        'time_out' => $timeOut,
                        'worked_hours' => $worked,
                        'plan_hours' => 8,
                        'deviation' => $deviation,
                        'absence_type' => null,
                    ]
                );
            }
        }
    }
}
