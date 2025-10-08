<?php

namespace App\Services;

use App\Models\Employee;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class PlanHoursService
{
    public function fetch(int $month, int $year)
    {
        $date = Carbon::create($year, $month);
        $beginDate = $date->format('d.m.Y H:i:s');
        $endingDate = $date->endOfMonth()->format('d.m.Y H:i:s');

        try {
//            $response = Http::withHeaders([
//                'Authorization' => env('ZUP_API_TOKEN'),
//                'Content-Type' => 'application/json',
//            ])
//            ->timeout(10)
//            ->send('GET', env('ZUP_API_URL'), [
//                'body' => json_encode([
//                    'BeginDate'  => $beginDate,
//                    'EndingDate' => $endingDate,
//                ]),
//            ]);


//            if (!$response->successful()) {
//                Log::error('Ошибка при запросе плановых часов из 1С', [
//                    'status' => $response->status(),
//                    'body' => $response->body(),
//                ]);
//                return null;
//            }
//
//            $data = $response->json('employees', []);

            $data = '[{"surname":"Глазов","name":"Юрий","scheduled_hours":0,"details":[]},{"surname":"Лобачева","name":"Александра","scheduled_hours":0,"details":[]},{"surname":"Журавлёв","name":"Павел","scheduled_hours":0,"details":[]},{"surname":"Палфёров","name":"Георгий","scheduled_hours":0,"details":[]},{"surname":"Пушина","name":"Елена","scheduled_hours":184,"details":[{"date":"2025-10-01T00:00:00","hours":8},{"date":"2025-10-02T00:00:00","hours":8},{"date":"2025-10-03T00:00:00","hours":8},{"date":"2025-10-06T00:00:00","hours":8},{"date":"2025-10-07T00:00:00","hours":8},{"date":"2025-10-08T00:00:00","hours":8},{"date":"2025-10-09T00:00:00","hours":8},{"date":"2025-10-10T00:00:00","hours":8},{"date":"2025-10-13T00:00:00","hours":8},{"date":"2025-10-14T00:00:00","hours":8},{"date":"2025-10-15T00:00:00","hours":8},{"date":"2025-10-16T00:00:00","hours":8},{"date":"2025-10-17T00:00:00","hours":8},{"date":"2025-10-20T00:00:00","hours":8},{"date":"2025-10-21T00:00:00","hours":8},{"date":"2025-10-22T00:00:00","hours":8},{"date":"2025-10-23T00:00:00","hours":8},{"date":"2025-10-24T00:00:00","hours":8},{"date":"2025-10-27T00:00:00","hours":8},{"date":"2025-10-28T00:00:00","hours":8},{"date":"2025-10-29T00:00:00","hours":8},{"date":"2025-10-30T00:00:00","hours":8},{"date":"2025-10-31T00:00:00","hours":8}]},{"surname":"Зенкова","name":"Екатерина","scheduled_hours":0,"details":[]},{"surname":"Кобец","name":"Анна","scheduled_hours":0,"details":[]},{"surname":"Скачков","name":"Александр","scheduled_hours":0,"details":[]},{"surname":"Стативка","name":"Никита","scheduled_hours":0,"details":[]},{"surname":"Журавлёв","name":"Анатолий","scheduled_hours":0,"details":[]},{"surname":"Козыревская","name":"Вера","scheduled_hours":0,"details":[]},{"surname":"Алексеев","name":"Никита","scheduled_hours":0,"details":[]},{"surname":"Палфёров","name":"Сергей","scheduled_hours":0,"details":[]},{"surname":"Брыкова","name":"Анна","scheduled_hours":0,"details":[]},{"surname":"Чернышова","name":"Екатерина","scheduled_hours":0,"details":[]},{"surname":"Романцова","name":"Ольга","scheduled_hours":0,"details":[]},{"surname":"Андриевская","name":"Людмила","scheduled_hours":184,"details":[{"date":"2025-10-01T00:00:00","hours":8},{"date":"2025-10-02T00:00:00","hours":8},{"date":"2025-10-03T00:00:00","hours":8},{"date":"2025-10-06T00:00:00","hours":8},{"date":"2025-10-07T00:00:00","hours":8},{"date":"2025-10-08T00:00:00","hours":8},{"date":"2025-10-09T00:00:00","hours":8},{"date":"2025-10-10T00:00:00","hours":8},{"date":"2025-10-13T00:00:00","hours":8},{"date":"2025-10-14T00:00:00","hours":8},{"date":"2025-10-15T00:00:00","hours":8},{"date":"2025-10-16T00:00:00","hours":8},{"date":"2025-10-17T00:00:00","hours":8},{"date":"2025-10-20T00:00:00","hours":8},{"date":"2025-10-21T00:00:00","hours":8},{"date":"2025-10-22T00:00:00","hours":8},{"date":"2025-10-23T00:00:00","hours":8},{"date":"2025-10-24T00:00:00","hours":8},{"date":"2025-10-27T00:00:00","hours":8},{"date":"2025-10-28T00:00:00","hours":8},{"date":"2025-10-29T00:00:00","hours":8},{"date":"2025-10-30T00:00:00","hours":8},{"date":"2025-10-31T00:00:00","hours":8}]},{"surname":"Оверина","name":"Анна","scheduled_hours":0,"details":[]},{"surname":"Леонтьева","name":"Ольга","scheduled_hours":0,"details":[]},{"surname":"Буренков","name":"Дмитрий","scheduled_hours":184,"details":[{"date":"2025-10-01T00:00:00","hours":8},{"date":"2025-10-02T00:00:00","hours":8},{"date":"2025-10-03T00:00:00","hours":8},{"date":"2025-10-06T00:00:00","hours":8},{"date":"2025-10-07T00:00:00","hours":8},{"date":"2025-10-08T00:00:00","hours":8},{"date":"2025-10-09T00:00:00","hours":8},{"date":"2025-10-10T00:00:00","hours":8},{"date":"2025-10-13T00:00:00","hours":8},{"date":"2025-10-14T00:00:00","hours":8},{"date":"2025-10-15T00:00:00","hours":8},{"date":"2025-10-16T00:00:00","hours":8},{"date":"2025-10-17T00:00:00","hours":8},{"date":"2025-10-20T00:00:00","hours":8},{"date":"2025-10-21T00:00:00","hours":8},{"date":"2025-10-22T00:00:00","hours":8},{"date":"2025-10-23T00:00:00","hours":8},{"date":"2025-10-24T00:00:00","hours":8},{"date":"2025-10-27T00:00:00","hours":8},{"date":"2025-10-28T00:00:00","hours":8},{"date":"2025-10-29T00:00:00","hours":8},{"date":"2025-10-30T00:00:00","hours":8},{"date":"2025-10-31T00:00:00","hours":8}]},{"surname":"Корогодов","name":"Глеб","scheduled_hours":0,"details":[]},{"surname":"Черняк","name":"Надежда","scheduled_hours":0,"details":[]},{"surname":"Шалабин","name":"Константин","scheduled_hours":0,"details":[]},{"surname":"Алетина","name":"Марина","scheduled_hours":0,"details":[]},{"surname":"Бондарь","name":"Егор","scheduled_hours":0,"details":[]},{"surname":"Левин","name":"Константин","scheduled_hours":0,"details":[]},{"surname":"Дычко","name":"Дарья","scheduled_hours":0,"details":[]},{"surname":"Перли","name":"Денис","scheduled_hours":0,"details":[]},{"surname":"Кукушкин","name":"Сергей","scheduled_hours":0,"details":[]},{"surname":"Никифоренко","name":"Кристина","scheduled_hours":0,"details":[]},{"surname":"Ящук","name":"Валерий","scheduled_hours":0,"details":[]},{"surname":"Максимов","name":"Алексей","scheduled_hours":0,"details":[]},{"surname":"Калягина","name":"Ирина","scheduled_hours":0,"details":[]},{"surname":"Котова","name":"Дарья","scheduled_hours":0,"details":[]},{"surname":"Бабай","name":"Павел","scheduled_hours":0,"details":[]},{"surname":"Комиссарова","name":"Анастасия","scheduled_hours":0,"details":[]},{"surname":"Афанасьев","name":"Игорь","scheduled_hours":0,"details":[]},{"surname":"Андреев","name":"Евгений","scheduled_hours":0,"details":[]},{"surname":"Химченко","name":"Екатерина","scheduled_hours":0,"details":[]},{"surname":"Ли","name":"Юрий","scheduled_hours":0,"details":[]},{"surname":"Сарапина","name":"Алёна","scheduled_hours":0,"details":[]},{"surname":"Косулько","name":"Оксана","scheduled_hours":0,"details":[]},{"surname":"Девяткина","name":"Анастасия","scheduled_hours":0,"details":[]},{"surname":"Миронов","name":"Владимир","scheduled_hours":0,"details":[]},{"surname":"Тупота","name":"Алексей","scheduled_hours":0,"details":[]},{"surname":"Нежданов","name":"Руслан","scheduled_hours":0,"details":[]},{"surname":"Данилов","name":"Евгений","scheduled_hours":184,"details":[{"date":"2025-10-01T00:00:00","hours":8},{"date":"2025-10-02T00:00:00","hours":8},{"date":"2025-10-03T00:00:00","hours":8},{"date":"2025-10-06T00:00:00","hours":8},{"date":"2025-10-07T00:00:00","hours":8},{"date":"2025-10-08T00:00:00","hours":8},{"date":"2025-10-09T00:00:00","hours":8},{"date":"2025-10-10T00:00:00","hours":8},{"date":"2025-10-13T00:00:00","hours":8},{"date":"2025-10-14T00:00:00","hours":8},{"date":"2025-10-15T00:00:00","hours":8},{"date":"2025-10-16T00:00:00","hours":8},{"date":"2025-10-17T00:00:00","hours":8},{"date":"2025-10-20T00:00:00","hours":8},{"date":"2025-10-21T00:00:00","hours":8},{"date":"2025-10-22T00:00:00","hours":8},{"date":"2025-10-23T00:00:00","hours":8},{"date":"2025-10-24T00:00:00","hours":8},{"date":"2025-10-27T00:00:00","hours":8},{"date":"2025-10-28T00:00:00","hours":8},{"date":"2025-10-29T00:00:00","hours":8},{"date":"2025-10-30T00:00:00","hours":8},{"date":"2025-10-31T00:00:00","hours":8}]}]';
            $data = json_decode($data, true);

            $this->update($data, $month, $year);
        } catch (Throwable $e) {
            Log::error('Ошибка при обращении к 1С: ' . $e->getMessage());
        }
    }

    public function update(array $data, int $month, int $year)
    {
        foreach ($data as $item) {
            $name = ($item['surname'] ?? null) . ' ' . ($item['name'] ?? null);
            $employee = Employee::firstWhere('name', $name);

            if (!$employee || empty($item['details'])) {
                continue;
            }

            foreach ($item['details'] as $detail) {
                $employee->planHours()->updateOrCreate(
                    ['employee_id' => $employee->id, 'date' => $detail['date']],
                    ['hours' => $detail['hours'] ?? 8]
                );
            }
        }
    }
}
