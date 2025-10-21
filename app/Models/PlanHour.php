<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PlanHour extends Model
{
    protected $guarded = [];
    protected $casts = ['date' => 'date'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function scopeByDateRange(Builder $q, $from = null, $to = null): Builder
    {
        return $q
            ->when($from && $to, fn($q) => $q->whereBetween('date', [$from, $to]))
            ->when($from && !$to, fn($q) => $q->where('date', '>=', $from))
            ->when(!$from && $to, fn($q) => $q->where('date', '<=', $to));
    }

    public function scopeByEmployeeId(Builder $q, int $id)
    {
        return $q->where('employee_id', $id);
    }
}
