<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class AttendanceService
{
    /**
     * @param Collection $times
     * @return array
     */
    public function calculateTime(Collection $times): array
    {
        $timeIn = null;
        $timeOut = null;

        if ($times->count() >= 2) {
            $timeIn = $times->min();
            $timeOut = $times->max();
        } elseif ($times->count() === 1) {
            $single = $times->first();
            if ($single < '13:00:00') {
                $timeIn = $single;
            } else {
                $timeOut = $single;
            }
        }

        return compact('timeIn', 'timeOut');
    }

    /**
     * @param string|null $timeIn
     * @param string|null $timeOut
     * @param string|null $date
     * @return string
     */
    public function calculateWorkedHours(?string $timeIn, ?string $timeOut, ?string $date = null): string
    {
        if (!$timeIn || !$timeOut) return '00:00:00';

        $baseDate = $date ?? now()->toDateString();

        $in = Carbon::createFromFormat('Y-m-d H:i:s', $baseDate . ' ' . $timeIn);
        $out = Carbon::createFromFormat('Y-m-d H:i:s', $baseDate . ' ' . $timeOut);

        $seconds = $in->diffInSeconds($out);
        $seconds = max($seconds - 3600, 0);

        return gmdate('H:i:s', $seconds);
    }


    /**
     * @param string|null $timeIn
     * @param string|null $timeOut
     * @param string|null $worked
     * @param int|null $plan
     * @return string|null
     */
    public function determineDeviation(?string $timeIn, ?string $timeOut, ?string $worked, int $plan = null): ?string
    {
        $deviations = [];

        if ($timeIn && strtotime($timeIn) > strtotime('10:00:00')) $deviations[] = 'Опоздание';
        if ($timeOut && strtotime($timeOut) < strtotime('18:00:00')) $deviations[] = 'Ранний уход';
        if ($worked) {
            [$h, $m, $s] = explode(':', $worked);
            if (!empty($plan)) {
                if ($h < $plan) {
                    $deviations[] = 'Неполный день';
                }
            } else {
                if ($h <= 1) {
                    $deviations[] = 'Без отметки';
                } elseif ($h < 8) {
                    $deviations[] = 'Неполный день';
                }
            }
        }

        return count($deviations) ? implode(',', $deviations) : null;
    }
}
