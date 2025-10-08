<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PlanHour;
use App\Models\Employee;
use Carbon\Carbon;

class PlanHourSeeder extends Seeder
{
    public function run(): void
    {
        $month = Carbon::now()->startOfMonth()->format('Y-m-d');

        foreach (Employee::all() as $emp) {
            PlanHour::updateOrCreate(
                ['employee_id' => $emp->id, 'month' => $month],
                ['hours' => 160] // 20 рабочих дней * 8 часов
            );
        }
    }
}
