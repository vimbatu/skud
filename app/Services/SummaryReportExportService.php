<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SummaryReportExportService
{
    public function __construct(
        private readonly SummaryReportService $summary
    ) {}

    public function prepare(Request $request): array
    {
        $rows = [[
            'Месяц / год',
            'Подразделение',
            'Сотрудник',
            'Часы (факт)',
            'План',
            'Средний приход',
            'Средний уход',
        ]];

        $collection = $this->summary->getSummary(
            $request->get('employee'),
            $request->get('department'),
            $request->get('date'),
            $request->boolean('only_deviations')
        );

        foreach ($collection as $r) {
            $rows[] = [
                $r['month_year'],
                $r['department'],
                $r['employee_name'],
                ExcelExportService::formatHours($r['fact_hours']),
                ExcelExportService::formatHours($r['plan_hours']),
                $r['avg_in'],
                $r['avg_out'],
            ];
        }

        return [$rows, $this->makeFileName($request)];
    }

    private function makeFileName(Request $request): string
    {
        $parts = ['skud_summary'];

        if ($request->filled('date')) {
            try {
                $dt = \Carbon\Carbon::createFromLocaleFormat('F Y', 'ru', $request->get('date'));
                $parts[] = $dt->format('m.Y');
            } catch (\Throwable) {
                $parts[] = Str::slug($request->get('date'), '_');
            }
        }

        if ($request->filled('department')) {
            $parts[] = 'dep_' . Str::slug($request->get('department'), '_');
        }

        if ($request->filled('employee')) {
            $parts[] = Str::slug($request->get('employee'), '_');
        }

        if ($request->boolean('only_deviations')) {
            $parts[] = 'otkloneniya';
        }

        return implode('_', $parts) . '.xlsx';
    }
}
