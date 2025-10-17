<?php

namespace App\Console\Commands;

use App\Services\PlanHoursService;
use Illuminate\Console\Command;
use Throwable;

class ImportPlanHours extends Command
{
    protected $signature = 'plan:import {month?} {year?}';
    protected $description = 'Import plan hours from 1C for sub month';

    /**
     * @param PlanHoursService $service
     * @return int
     */
    public function handle(PlanHoursService $service): int
    {
        $target = now()->subMonth();
        $month  = $this->argument('month') ?? $target->format('m');
        $year   = $this->argument('year') ?? $target->format('Y');

        try {
            $service->fetch($month, $year);
        } catch (Throwable $e) {
            $this->error('Ошибка импорта: ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->info('Import finished');
        return self::SUCCESS;
    }
}
