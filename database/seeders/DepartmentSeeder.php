<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = ['IT', 'HR', 'Финансы', 'Продажи'];

        foreach ($departments as $d) {
            Department::firstOrCreate(['name' => $d]);
        }
    }
}
