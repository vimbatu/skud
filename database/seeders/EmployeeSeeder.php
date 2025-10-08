<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Department;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $it = Department::where('name', 'IT')->first();
        $hr = Department::where('name', 'HR')->first();

        $employees = [
            ['last_name' => 'Иванов', 'first_name' => 'Игорь', 'department_id' => $it->id, 'position' => 'Разработчик'],
            ['last_name' => 'Петрова', 'first_name' => 'Анна', 'department_id' => $hr->id, 'position' => 'HR-менеджер'],
            ['last_name' => 'Сидоров', 'first_name' => 'Владимир', 'department_id' => $it->id, 'position' => 'Системный администратор'],
        ];

        foreach ($employees as $e) {
            Employee::firstOrCreate([
                'last_name' => $e['last_name'],
                'first_name' => $e['first_name']
            ], $e);
        }
    }
}
