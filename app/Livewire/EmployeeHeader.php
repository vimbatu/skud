<?php

namespace App\Livewire;

use App\Models\Department;
use App\Models\Employee;
use App\Services\PlanHoursService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Throwable;

class EmployeeHeader extends Component
{
    public string $name = '';
    public ?int $department_id = null;
    public ?int $plan_hours = null;
    public string $position = '';
    public int $month;
    public int $year;
    public Collection $departments;
    public function mount(): void
    {
        $this->departments = Department::orderBy('name')->get();
        $this->month = now()->month;
        $this->year = now()->year;
    }

    public function addEmployee(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'plan_hours' => 'required|integer|min:0',
            'position' => 'nullable|string|max:255',
        ]);

        $employee = Employee::create([
            'name' => $this->name,
            'department_id' => $this->department_id,
            'position' => $this->position,
        ]);

        $employee->planHours()->updateOrCreate(
            ['employee_id' => $employee->id, 'month' => now()->startOfMonth()],
            ['hours' => $this->plan_hours]
        );

        $this->reset(['name', 'department_id', 'plan_hours', 'position']);
        $this->dispatch('update');
    }

    public function syncPlan(PlanHoursService $service): void
    {
        try {
            $service->fetch($this->month, $this->year);
            $this->js("window.location.reload()");
        } catch (Throwable $e) {
            Log::error('Ошибка синхронизации плана: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.employee-header');
    }
}
