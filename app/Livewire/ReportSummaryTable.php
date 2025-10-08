<?php

namespace App\Livewire;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Livewire\Component;
use Livewire\WithPagination;

class ReportSummaryTable extends Component
{
    use WithPagination;

    public $sortBy = 'date';
    public $sortDirection = 'desc';
    public $perPage = 300;

    public $from;
    public $to;
    public $employee;
    public $only_deviations = false;

//    protected $queryString = [
//        'from',
//        'to',
//        'employee',
//        'only_deviations' => ['except' => false],
//        'page',
//    ];

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
        $records = Attendance::byDateRange($this->from, $this->to)
            ->byEmployee($this->employee)
            ->when($this->only_deviations, fn($q) => $q->withDeviations());

        if ($this->sortDirection === 'asc') {
            $records->orderByRaw("CASE WHEN {$this->sortBy} IS NULL THEN 0 ELSE 1 END ASC")
                ->orderByColumn($this->sortBy, 'asc');
        } else {
            $records->orderByRaw("CASE WHEN {$this->sortBy} IS NULL THEN 1 ELSE 0 END ASC")
                ->orderByColumn($this->sortBy, 'desc');
        }

        $attendances = $records
            ->paginate($this->perPage);

        return view('livewire.report-summary-table', [
            'attendances' => $attendances,
        ]);
    }
}
