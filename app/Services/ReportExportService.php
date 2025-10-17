<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReportExportService
{
    /**
     * @param Request $request
     * @return array
     */
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
            )
            ->orderBy('date');

        $records = $query->get()->keyBy(fn($a) => $a->date->toDateString());

        $from = $request->filled('from') ? Carbon::parse($request->from) : $records->min('date') ?? now()->startOfMonth();
        $to   = $request->filled('to') ? Carbon::parse($request->to) : $records->max('date') ?? now()->endOfMonth();

        $employee = $records->first()?->employee;
        if (!$employee && $request->filled('employee_id')) {
            $employee = Employee::find($request->employee_id);
        }

        $allDates = collect();
        for ($date = $from->copy(); $date->lte($to); $date->addDay()) {
            if (!$date->isWeekend()) {
                $allDates->push($date->copy());
            }
        }

        foreach ($allDates as $date) {
            $a = $records->get($date->toDateString());

            $plan = $employee
                ? $employee->planHours()->where('date', $date->toDateString())->value('hours') ?? 8
                : 8;

            $isAbsent = !$a;

            $rows[] = [
                $date->format('d.m.Y'),
                $employee?->name ?? ($a?->employee?->name ?? '—'),
                $a?->time_in ?? '—',
                $a?->time_out ?? '—',
                $a?->worked_hours ?? '00:00:00',
                $isAbsent
                    ? '-'.str_pad((int)$plan, 2, '0', STR_PAD_LEFT).':00:00'
                    : ExcelExportService::deviationTime($a->worked_hours, $plan),
                $plan,
                $isAbsent ? 'Без отметки' : ($a->deviation ?? '—'),
                $a?->absence_type ?? '—',
            ];
        }


        return [$rows, $this->makeFileName($request)];
    }


    /**
     * @param Request $request
     * @return string
     */
    private function makeFileName(Request $request): string
    {
        $parts = ['skud'];

        if ($request->filled('employee')) {
            $parts[] = Str::slug(
                Employee::where('name', 'like', '%' . $request->employee . '%')
                    ->first()
                    ->name,
                '_'
            );
        }

        if ($request->filled('employee_id')) {
            $parts[] = Str::slug(Employee::find($request->employee_id)->name, '_');
        }

        if ($request->filled('from') && !$request->filled('to')) {
            $parts[] = 'from_' . Carbon::parse($request->from)->format('d.m.Y');
        }

        if (!$request->filled('from') && $request->filled('to')) {
            $parts[] = 'to_' . Carbon::parse($request->to)->format('d.m.Y');
        }

        if ($request->filled('from') && $request->filled('to')) {
            $parts[] = Carbon::parse($request->from)->format('d.m.Y')
                . '-' .
                Carbon::parse($request->to)->format('d.m.Y');
        }

        if ($request->boolean('only_deviations')) {
            $parts[] = 'otkloneniya';
        }

        return implode('_', $parts) . '.xlsx';
    }
}
