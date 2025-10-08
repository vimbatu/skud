<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DepartmentSeeder::class,
            EmployeeSeeder::class,
            PlanHourSeeder::class,
            AdminSeeder::class,
            AttendanceSeeder::class,
        ]);
    }
}
