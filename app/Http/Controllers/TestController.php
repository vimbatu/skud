<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function execute(Request $request)
    {
        $cursor = now()->startOfMonth();
        $monthEnd   = now()->endOfMonth();

        $employee = Employee::firstWhere('name', 'Алётина Марина');

        while ($cursor->lte($monthEnd)) {
            if (!in_array($cursor->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY])) {
                $employee->planHours()->updateOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'month' => $cursor->toDateString()
                    ],
                    ['hours' => 8]
                );
            }
            $cursor->addDay();
        }
    }
}
