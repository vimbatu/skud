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

    public function handle(KedrService $service, KedrEmployeeMapper $mapper)
    {
        try {
            $employees = $service->getEmployeesList();

            foreach ($employees as $emp) {
                $mapped = $mapper->map($emp);

                if (
                    !$mapped['name']
                    || !$mapped['position']
                ) continue;

                Employee::updateOrCreate($mapped);
            }
        } catch (KedrApiException $e) {
            $this->error($e->getMessage());
        }

        $this->info('Import finished');
    }
}
