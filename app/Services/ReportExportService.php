<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportExportService
{
    public function prepare(Request $request): array
    {
        $rows = [[
            'Дата', 'Сотрудник', 'Приход', 'Уход', 'Часы (факт)', 'Отклонение по времени', 'План', 'Отклонение', 'Вид отсутствия'
        ]];

        $query = Attendance::with('employee')
            ->when($request->filled('from') && $request->filled('to'),
                fn($q) => $q->byDateRange($request->from, $request->to)
            )
            ->when($request->filled('employee_id'),
                fn($q) => $q->byEmployee($request->employee_id)
            )
            ->when($request->filled('employee'),
                fn($q) => $q->byEmployee($request->employee)
            )
            ->when($request->boolean('only_deviations'),
                fn($q) => $q->withDeviations()
            );

        $sortBy = $request->get('sortBy', 'date');
        $sortDirection = $request->get('sortDirection', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        foreach ($query->get() as $a) {
            $rows[] = [
                $a->date?->format('d.m.Y'),
                $a->employee?->name ?? '—',
                $a->time_in,
                $a->time_out,
                $a->worked_hours,
                ExcelExportService::deviationTime($a->worked_hours, $a->plan_hours ?? 8),
                $a->plan_hours ?? 8,
                $a->deviation,
                $a->absence_type,
            ];
        }

        return [$rows, $this->makeFileName($request)];
    }

    private function makeFileName(Request $request): string
    {
        $parts = ['skud'];

        if ($request->filled('from') && $request->filled('to')) {
            $parts[] = Carbon::parse($request->from)->format('d.m.Y')
                . '-' .
                Carbon::parse($request->to)->format('d.m.Y');
        }

        if ($request->filled('employee')) {
            $parts[] = str_replace(' ', '_', $request->employee);
        }

        if ($request->filled('employee_id')) {
            $parts[] = str_replace(' ', '_', Employee::find($request->employee_id)->name);
        }

        if ($request->boolean('only_deviations')) {
            $parts[] = 'otkloneniya';
        }

        return implode('_', $parts) . '.xlsx';
    }
}
