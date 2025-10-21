<?php

namespace App\Livewire;

use App\Models\AbsenceType;
use App\Models\Attendance;
use App\Services\AttendanceService;
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
        $records = Attendance::byEmployee($this->employeeId)
            ->byDateRange($this->from, $this->to)
            ->when($this->only_deviations, fn($q) => $q->withDeviations())
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(50);

        $all = clone $records;

        $avgTimeIn = $all->whereNotNull('time_in')->avg(fn($r) => strtotime($r->time_in));
        $avgTimeOut = $all->whereNotNull('time_out')->avg(fn($r) => strtotime($r->time_out));
        $avgWorked = $all->whereNotNull('worked_hours')->avg(fn($r) => strtotime($r->worked_hours));

        $avgTimeIn = $avgTimeIn ? date('H:i:s', (int)$avgTimeIn) : null;
        $avgTimeOut = $avgTimeOut ? date('H:i:s', (int)$avgTimeOut) : null;
        $avgWorked = $avgWorked ? date('H:i:s', (int)$avgWorked) : null;

        $absences = AbsenceType::all()->pluck('name');

        return view('livewire.report-detail-table', compact('records', 'avgTimeIn', 'avgTimeOut', 'avgWorked', 'absences'));
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
