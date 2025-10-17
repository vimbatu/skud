<?php

namespace App\Services;

use App\Models\Employee;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class PlanHoursService
{
    /**
     * @param int $month
     * @param int $year
     * @return void
     */
    public function fetch(int $month, int $year): void
    {
        $date = Carbon::create($year, $month);
        $beginDate = $date->format('d.m.Y H:i:s');
        $endingDate = $date->endOfMonth()->format('d.m.Y H:i:s');

        try {
            $response = Http::withHeaders([
                'Authorization' => env('ZUP_API_TOKEN'),
                'Content-Type' => 'application/json',
            ])
                ->timeout(10)
                ->post(env('ZUP_API_URL'), [
                    'BeginDate' => $beginDate,
                    'EndingDate' => $endingDate,
                ]);

            if (!$response->successful()) {
                Log::error('Ошибка при запросе плановых часов из 1С', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return;
            }

            $data = $response->json('employees', []);

            $this->update($data);
        } catch (Throwable $e) {
            Log::error('Ошибка при обращении к 1С: ' . $e->getMessage());
        }
    }

    /**
     * @param array $data
     * @return void
     */
    public function update(array $data): void
    {
        foreach ($data as $item) {
            $name = ($item['surname'] ?? null) . ' ' . ($item['name'] ?? null);
            $employee = Employee::firstWhere('name', $name);

            if (!$employee || empty($item['details'])) {
                continue;
            }

            foreach ($item['details'] as $detail) {
                $rawHours = $detail['hours'] ?? null;

                $hours = trim((string) $rawHours);

                if ($hours === '' || !is_numeric($hours) || $hours < 0) {
                    $hours = 8;
                }

                $employee->planHours()->updateOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'date' => $detail['date'],
                    ],
                    [
                        'hours' => (float) $hours,
                    ]
                );
            }
        }
    }
}
