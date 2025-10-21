<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\PlanHour;
use Illuminate\Console\Command;

class FillMissingAttendances extends Command
{
    protected $signature = 'attendance:fill-missing
        {--from= : Дата начала периода (YYYY-MM-DD)}
        {--to= : Дата окончания периода (YYYY-MM-DD)}';

    protected $description = 'Добавляет в Attendance отсутствующие записи для всех плановых дней (по всем сотрудникам)';

    public function handle(): int
    {
        $from = $this->option('from');
        $to = $this->option('to');

        $this->info('Поиск недостающих записей...');

        $plans = PlanHour::query()
            ->when($from || $to, fn($q) => $q->byDateRange($from, $to))
            ->select(['employee_id', 'date', 'hours'])
            ->orderBy('employee_id')
            ->orderBy('date')
            ->get();

        if ($plans->isEmpty()) {
            $this->info('Плановые часы не найдены.');
            return Command::SUCCESS;
        }

        $created = 0;
        $batch = [];

        foreach ($plans as $plan) {
            $exists = Attendance::query()
                ->where('employee_id', $plan->employee_id)
                ->whereDate('date', $plan->date)
                ->exists();

            if (!$exists) {
                $batch[] = [
                    'employee_id' => $plan->employee_id,
                    'date' => $plan->date,
                    'time_in' => null,
                    'time_out' => null,
                    'worked_hours' => '00:00:00',
                    'deviation' => 'Без отметки',
                    'absence_type' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (count($batch) >= 1000) {
                Attendance::insert($batch);
                $created += count($batch);
                $batch = [];
            }
        }

        if (!empty($batch)) {
            Attendance::insert($batch);
            $created += count($batch);
        }

        $this->info("Добавлено $created записей в Attendance.");
        return Command::SUCCESS;
    }
}
