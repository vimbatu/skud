<?php

namespace App\Services;

use App\Models\Attendance;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Throwable;

class SummaryReportService
{
    public function getSummary(
        ?string $employee = null,
        ?string $department = null,
        ?string $date = null,
        bool    $onlyDeviations = false
    ): Collection
    {
        $attendances = $this->loadAttendances($employee, $department, $date);

        $summary = $attendances
            ->groupBy(fn($a) => $a->employee_id . '-' . $a->date->format('Y-m'))
            ->map(fn($group) => $this->buildRow($group))
            ->values();

        return $onlyDeviations
            ? $summary->where('has_deviation', true)->values()
            : $summary;
    }

    protected function loadAttendances(?string $employee, ?string $department, ?string $date): Collection
    {
        return Attendance::query()
            ->with(['employee.department', 'employee.planHours'])
            ->byEmployee($employee)
            ->byDepartment($department)
            ->when($date, function ($q) use ($date) {
                try {
                    $dateParsed = Carbon::createFromLocaleFormat('F Y', 'ru', $date);
                } catch (Throwable) {
                    return $q;
                }

                return $q->byDateRange(
                    $dateParsed->copy()->startOfMonth()->toDateString(),
                    $dateParsed->copy()->endOfMonth()->toDateString()
                );
            })
            ->orderBy('date', 'desc')
            ->get();
    }

    protected function buildRow(Collection $group): array
    {
        $first = $group->first();
        $plan = $this->calcPlanHours($first);
        $fact = $this->calcFactHours($group);

        return [
            'month_year' => mb_ucfirst($first->date->translatedFormat('F Y')),
            'month_key' => $first->date->format('Y-m'),
            'department' => $first->employee->department->name ?? '—',
            'employee' => $first->employee,
            'employee_name' => $first->employee->name,
            'plan_hours' => $plan,
            'fact_hours' => $fact,
            'avg_in' => $this->calcAverageTime($group, 'time_in'),
            'avg_out' => $this->calcAverageTime($group, 'time_out'),
            'has_deviation' => $fact < $plan,
        ];
    }

    protected function calcPlanHours($attendance): float
    {
        $monthStart = $attendance->date->copy()->startOfMonth()->toDateString();
        $monthEnd = $attendance->date->copy()->endOfMonth()->toDateString();

        return round(
            $attendance->employee->planHours
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->sum('hours'),
            1
        );
    }

    protected function calcFactHours(Collection $group): float
    {
        return round($group->sum(fn($a) => (float)$a->worked_hours), 1);
    }

    protected function calcAverageTime(Collection $group, string $field): string
    {
        $avg = $group
            ->filter(fn($a) => $a->{$field})
            ->avg(fn($a) => strtotime($a->{$field}));

        return $avg ? date('H:i:s', $avg) : '—';
    }
}
