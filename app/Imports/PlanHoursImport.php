<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\PlanHour;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;

class PlanHoursImport implements OnEachRow, WithHeadingRow
{
    public function onRow(Row $row)
    {
        $r = $row->toArray();
        // ожидаем колонки: employee_tn (таб.номер) or last_name, month (YYYY-MM), hours
        $employee = null;
        if (!empty($r['employee_id'])) $employee = Employee::find($r['employee_id']);
        if (!$employee && !empty($r['last_name'])) {
            $employee = Employee::where('last_name', $r['last_name'])->first();
        }
        if (!$employee) return;
        PlanHour::updateOrCreate(['employee_id' => $employee->id, 'month' => $r['month']], ['hours' => floatval($r['hours'])]);
    }
}
