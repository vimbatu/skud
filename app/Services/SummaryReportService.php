<?php

namespace App\Services;

use App\Models\Attendance;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Throwable;

class SummaryReportService
{
    /**
     * @param string|null $employee
     * @param string|null $department
     * @param string|null $month
     * @param string|null $year
     * @param bool $onlyDeviations
     * @return Collection
     */
    public function getSummary(
        ?string $employee = null,
        ?string $department = null,
        ?string $month = null,
        ?string $year = null,
        bool    $onlyDeviations = false
    ): Collection
    {
        $attendances = $this->loadAttendances($employee, $department, $month, $year);

        $summary = $attendances
            ->groupBy(fn($a) => $a->employee_id . '-' . $a->date->format('Y-m'))
            ->map(fn($group) => $this->buildRow($group))
            ->values();

        return $onlyDeviations
            ? $summary->where('has_deviation', true)->values()
            : $summary;
    }

    /**
     * @param string|null $employee
     * @param string|null $department
     * @param string|null $month
     * @param string|null $year
     * @return Collection
     */
    protected function loadAttendances(?string $employee, ?string $department, ?string $month, ?string $year): Collection
    {
        return Attendance::query()
            ->with(['employee.department', 'employee.planHours'])
            ->byEmployee($employee)
            ->byDepartment($department)
            ->when($month && $year, function ($q) use ($month, $year) {
                $date = Carbon::create($year, $month);
                $start = $date->copy()->startOfMonth()->startOfDay();
                $end = $date->copy()->endOfMonth()->endOfDay();

                return $q->byDateRange($start, $end);
            })
            ->orderBy('date', 'desc')
            ->get();
    }

    /**
     * @param Collection $group
     * @return array
     */
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

    /**
     * @param $attendance
     * @return float
     */
    protected function calcPlanHours($attendance): float
    {
        $start = $attendance->date->copy()->startOfMonth()->startOfDay();
        $end = $attendance->date->copy()->endOfMonth()->endOfDay();

        return round(
            $attendance->employee->planHours
                ->whereBetween('date', [$start, $end])
                ->sum('hours'),
            1
        );
    }

    /**
     * @param Collection $group
     * @return float
     */
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
