<?php

namespace App\Livewire;

use App\Models\AbsenceType;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\PlanHour;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;

class ReportDetailTable extends Component
{
    use WithPagination;

    public $employeeId;
    public $from;
    public $to;
    public $only_deviations = false;

    public $sortBy = 'date';
    public $sortDirection = 'desc';

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        $employee = Employee::find($this->employeeId);
        if (!$employee) {
            abort(404, 'Сотрудник не найден');
        }

        $from = Carbon::parse($this->from ?? now()->startOfMonth());
        $to   = Carbon::parse($this->to ?? now()->endOfMonth());

        $allDates = collect();
        for ($date = $from->copy(); $date->lte($to); $date->addDay()) {
            if (!$date->isWeekend()) {
                $allDates->push($date->copy());
            }
        }

        $records = Attendance::byEmployee($this->employeeId)
            ->byDateRange($from, $to)
            ->when($this->only_deviations, fn($q) => $q->withDeviations())
            ->with(['employee.planHours' => fn($p) => $p->byDateRange($from, $to)])
            ->get()
            ->keyBy(fn($r) => $r->date->toDateString());

        $full = $allDates->map(function ($date) use ($records, $employee) {
            $record = $records->get($date->toDateString());

            return $record ?: new Attendance([
                'employee_id'  => $employee->id,
                'date'         => $date->copy(),
                'time_in'      => null,
                'time_out'     => null,
                'worked_hours' => '00:00:00',
                'plan_hours'   => $employee->planHours()->where('date', $date->toDateString())->value('hours') ?? null,
                'deviation'    => 'Без отметки',
                'absence_type' => null,
            ]);
        });

        $page = $this->getPage();
        $perPage = 50;
        $paginated = new LengthAwarePaginator(
            $full->forPage($page, $perPage)->values(),
            $full->count(),
            $perPage,
            $page
        );

        $avgTimeIn  = $records->whereNotNull('time_in')->avg(fn($r) => strtotime($r->time_in));
        $avgTimeOut = $records->whereNotNull('time_out')->avg(fn($r) => strtotime($r->time_out));
        $avgWorked  = $records->whereNotNull('worked_hours')->avg(fn($r) => strtotime($r->worked_hours));

        $avgTimeIn  = $avgTimeIn ? date('H:i:s', (int)$avgTimeIn) : null;
        $avgTimeOut = $avgTimeOut ? date('H:i:s', (int)$avgTimeOut) : null;
        $avgWorked  = $avgWorked ? date('H:i:s', (int)$avgWorked) : null;

        $absences = AbsenceType::pluck('name');

        return view('livewire.report-detail-table', [
            'records'    => $paginated,
            'avgTimeIn'  => $avgTimeIn,
            'avgTimeOut' => $avgTimeOut,
            'avgWorked'  => $avgWorked,
            'absences'   => $absences,
        ]);
    }

    public function updateHours(Attendance $record, string $hours, AttendanceService $service): void
    {
        if (empty($hours) && !empty($record->employee->planHours()->where('date', $record->date)->first())) {
            $record->employee->planHours()->where('date', $record->date)->delete();
        } else {
            $record->employee->planHours()->updateOrCreate(
                ['employee_id' => $record->employee_id, 'date' => $record->date],
                ['hours' => $hours]
            );
        }

        $deviation = $service->determineDeviation(
            $record->time_in,
            $record->time_out,
            $record->worked_hours,
            $hours ?: 8
        );

        $record->deviation = $deviation;
        $record->save();
    }

    public function updateAbsence(Attendance $record, ?string $absence = null): void
    {
        $record->absence_type = $absence;
        $record->save();
    }
}
