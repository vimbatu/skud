<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $guarded = [];
    protected $casts = ['date' => 'date'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function scopeOrderByColumn(Builder $query, string $column, string $direction = 'asc'): Builder
    {
        switch ($column) {
            case 'department_id':
                return $query
                    ->join('employees', 'employees.id', '=', 'attendances.employee_id')
                    ->leftJoin('departments', 'departments.id', '=', 'employees.department_id')
                    ->orderBy('departments.name', $direction)
                    ->select('attendances.*');

            default:
                return $query->orderBy("attendances.$column", $direction);
        }
    }

    public function scopeByDateRange(Builder $q, $from, $to)
    {
        return $q->when($from && $to, fn($q) => $q->whereBetween('date', [$from, $to]));
    }

    public function scopeWithDeviations(Builder $q)
    {
        return $q->whereNotNull('deviation');
    }

    public function scopeByEmployee(Builder $q, int|string $query = null)
    {
        return $q->when($query, function ($q) use ($query) {
            if (is_numeric($query)) {
                return $q->where('employee_id', $query);
            } else {
                return $q->whereHas('employee', function ($q) use ($query) {
                    $q->where('name', 'like', "%$query%");
                });
            }
        });
    }

    public function scopeByDepartment(Builder $q, ?string $query = null)
    {
        return $q->when($query, function ($q) use ($query) {
            return $q->whereHas('employee.department', function ($q) use ($query) {
                $q->where('name', 'like', "%$query%");
            });
        });
    }

    public function getTimeInColorAttribute(): string
    {
        return str_contains($this->deviation, 'опоздал') ? '!text-red-600' : '!text-green-600';
    }

    public function getTimeOutColorAttribute(): string
    {
        return str_contains($this->deviation, 'слинял') ? '!text-red-600' : '!text-green-600';
    }

    public function getWorkedHoursColorAttribute(): string
    {
        [$h, $m, $s] = explode(':', $this->worked_hours);
        return (str_contains($this->deviation, 'откосил') || $h < 8)
            ? '!text-red-600'
            : '!text-green-600';
    }

    public function getDeviationColorAttribute(): string
    {
        [$h, $m, $s] = explode(':', $this->worked_hours);

        $plan = $this->employee->planHours()
            ->where('date', $this->date)
            ->first()
            ?->hours ?? 8;

        return ($h < $plan)
            ? '!text-red-600'
            : '!text-green-600';
    }

    public function getPlanHoursStyleAttribute()
    {
        return $this->employee->planHours()
            ->where('date', $this->date)
            ->exists()
            ? 'color: green; font-weight: bold;'
            : 'color: #aaa;';
    }
}
