<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PlanHour extends Model
{
    protected $guarded = [];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function scopeByDateRange(Builder $q, $from, $to)
    {
        return $q->when($from && $to, fn($q) => $q->whereBetween('date', [$from, $to]));

    }
}
