<?php

namespace App\Console\Commands;

use App\Exceptions\KedrApiException;
use App\Models\Employee;
use App\Services\KedrEmployeeMapper;
use App\Services\KedrService;
use Illuminate\Console\Command;

class ImportKedrEmployees extends Command
{
    protected $signature = 'kedr:import-employees';
    protected $description = 'Import employees from Kedr.Cloud';

    /**
     * @param KedrService $service
     * @param KedrEmployeeMapper $mapper
     * @return int
     */
    public function handle(KedrService $service, KedrEmployeeMapper $mapper): int
    {
        try {
            $employees = $service->getEmployeesList();

            foreach ($employees as $emp) {
                $mapped = $mapper->map($emp);

                if (
                    !$mapped['name']
                    || !$mapped['position']
                ) continue;

                Employee::updateOrCreate(
                    ['name' => $mapped['name']],
                    ['position' => $mapped['position'], 'status' => $mapped['status']]
                );
            }
        } catch (KedrApiException $e) {
            $this->error('Ошибка импорта: ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->info('Import finished');
        return self::SUCCESS;
    }
}
