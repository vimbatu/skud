<?php

namespace App\Services;

use App\Models\Employee;
use Carbon\Carbon;

class KedrAttendanceMapper
{
    /**
     * @param array $data
     * @return array
     */
    public function map(array $data): array
    {
        $name = ($data['fam'] ?? '') . ' ' . ($data['name'] ?? '');
        $dateTime = Carbon::parse($data['datetime']);

        return [
            'employee_name' => $name,
            'employee_id' => Employee::byName($name)->first()?->id,
            'time' => $dateTime->format('H:i:s'),
            'date' => $dateTime->format('Y-m-d'),
        ];
    }
}
