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

        $avgTimeIn = $this->avgTime($all, 'time_in');
        $avgTimeOut = $this->avgTime($all, 'time_out');
        $avgWorked = $this->avgTime($all, 'worked_hours');

        $absences = AbsenceType::all()->pluck('name');

        return view('livewire.report-detail-table', compact('records', 'avgTimeIn', 'avgTimeOut', 'avgWorked', 'absences'));
    }

    private function avgTime($collection, string $field): ?string
    {
        $seconds = $collection
            ->whereNotNull($field)
            ->where($field, '!=', '00:00:00')
            ->avg(function ($item) use ($field) {
                [$h, $m, $s] = array_map('intval', explode(':', $item->$field));
                return $h * 3600 + $m * 60 + $s;
            });

        return $seconds ? gmdate('H:i:s', (int) round($seconds)) : null;
    }

    public function updateHours(Attendance $record, string $hours, AttendanceService $service): void
    {
        if (empty($hours) && !empty($record->employee->planHours()->where('date', $record->date)->first())) {
            $record->employee->planHours()->where('date', $record->date)->delete();
        } elseif (!empty($hours)) {
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
