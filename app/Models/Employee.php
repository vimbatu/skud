<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $guarded = [];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function planHours()
    {
        return $this->hasMany(PlanHour::class);
    }

    public function getFullNameAttribute()
    {
        return $this->name . ' ' . $this->department->name;
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function scopeActive(Builder $q)
    {
        return $q->where('status', 'active');
    }

    public function scopeRemote(Builder $q)
    {
        return $q->where('status', 'remote');
    }

    public function scopeByName(Builder $q, $name)
    {
        return $q->where('name', $name);
    }

    public function currentHours(Carbon $date): float
    {
        return (float) ($this->planHours()
            ->where('date', $date)
            ->first()
            ?->hours ?? 8);
    }
}
