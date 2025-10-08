<?php

namespace App\Livewire;

use App\Models\Department;
use App\Models\Employee;
use Livewire\Component;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;

class EmployeeTable extends Component
{
    public Collection $departments;
    public Collection $employees;

    #[On('update')]
    public function mount(): void
    {
        $this->loadData();
    }

    private function loadData(): void
    {
        $this->departments = Department::orderBy('name')->get();
        $this->employees = Employee::with([
                'department',
                'planHours',
            ])
            ->orderBy('name')
            ->get();
    }

    public function updateDepartment(Employee $employee, $departmentId): void
    {
        $employee->update(['department_id' => $departmentId ?: null]);
    }

    public function updatePosition(Employee $employee, $position): void
    {
        $employee->update(['position' => $position ?: null]);
    }

    public function delete(Employee $employee): void
    {
        $employee->delete();
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.employee-table');
    }
}
