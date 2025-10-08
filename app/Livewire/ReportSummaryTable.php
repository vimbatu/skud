<?php

namespace App\Livewire;

use App\Services\SummaryReportService;
use Livewire\Component;
use Livewire\WithPagination;

class ReportSummaryTable extends Component
{
    use WithPagination;

    public $sortBy = 'month_year';
    public $sortDirection = 'desc';
    public $perPage = 100;
    public $employee;
    public $department;
    public $date;
    public $only_deviations = false;

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function render(SummaryReportService $service)
    {
        $onlyDeviations = (bool)$this->only_deviations;

        $collection = $service->getSummary(
            $this->employee,
            $this->department,
            $this->date,
            $onlyDeviations
        );

        if ($onlyDeviations) {
            $collection = $collection->where('has_deviation', true);
        }

        $sortField = match ($this->sortBy) {
            'month_year' => 'month_key',
            'employee' => 'employee_name',
            default => $this->sortBy,
        };

        $collection = $collection->sortBy([[$sortField, $this->sortDirection]]);

        $page = $this->getPage();
        $items = $collection->forPage($page, $this->perPage)->values();
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $collection->count(),
            $this->perPage,
            $page
        );

        return view('livewire.report-summary-table', ['attendances' => $paginator]);
    }
}
