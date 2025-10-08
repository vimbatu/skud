<?php

namespace App\Console\Commands;

use App\Exceptions\KedrApiException;
use App\Models\Attendance;
use App\Services\AttendanceService;
use App\Services\KedrAttendanceMapper;
use App\Services\KedrService;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class ImportKedrData extends Command
{
    protected $signature = 'kedr:import {from?} {to?}';
    protected $description = 'Import SKUD data from Kedr.Cloud by period';

    /**
     * @param KedrService $service
     * @param KedrAttendanceMapper $mapper
     * @param AttendanceService $attendanceService
     * @return void
     */
    public function handle(
        KedrService          $service,
        KedrAttendanceMapper $mapper,
        AttendanceService    $attendanceService
    )
    {
        $from = $this->argument('from') ?? now()->subDays(2)->format('Y-m-d');
        $to = $this->argument('to') ?? now()->format('Y-m-d');

        try {
            $data = $service->getAccessProtocol($from, $to);
            $mapped = $this->map($data, $mapper, $attendanceService);

            foreach ($mapped as $row) {
                if (empty($row['employee_id'])) {
                    $this->warn("Пропущена запись: нет employee_id для {$row['employee_name']}");
                    continue;
                }

                $this->updateOrCreate($row);
            }
        } catch (KedrApiException $e) {
            $this->error($e->getMessage());
        }

        $this->info('Import finished');
    }

    /**
     * @param array $data
     * @param KedrAttendanceMapper $mapper
     * @param AttendanceService $attendanceService
     * @return array
     */
    private function map(array $data, KedrAttendanceMapper $mapper, AttendanceService $attendanceService): array
    {
        return collect($data)
            ->map(fn($item) => $mapper->map($item))
            ->groupBy(fn($i) => $i['employee_id'] . '_' . $i['date'])
            ->map(function (Collection $items) use ($attendanceService) {
                $first = $items->first();

                $times = $items->pluck('time')->sort();
                $timesCalculated = $attendanceService->calculateTime($times);
                $timeIn = $timesCalculated['timeIn'];
                $timeOut = $timesCalculated['timeOut'];

                $worked = $attendanceService->calculateWorkedHours($timeIn, $timeOut, $first['date']);
                $deviation = $attendanceService->determineDeviation($timeIn, $timeOut, $worked);

                return [
                    'employee_name' => $first['employee_name'],
                    'employee_id' => $first['employee_id'],
                    'date' => $first['date'],
                    'time_in' => $timeIn,
                    'time_out' => $timeOut,
                    'worked_hours' => $worked,
                    'deviation' => $deviation,
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * @param array $row
     * @return void
     */
    private function updateOrCreate(array $row): void
    {
        Attendance::updateOrCreate(
            [
                'employee_id' => $row['employee_id'],
                'date'        => $row['date'],
            ],
            [
                'time_in'     => $row['time_in'],
                'time_out'    => $row['time_out'],
                'worked_hours' => $row['worked_hours'],
                'deviation'   => $row['deviation'],
            ]
        );
    }
}
