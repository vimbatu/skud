<?php

namespace App\Services;

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;

class SummaryReportExportService
{
    public function __construct(
        private readonly SummaryReportService $summary
    ) {}

    /**
     * @param Request $request
     * @return array
     */
    public function prepare(Request $request): array
    {
        $rows = [[
            'Месяц / год',
            'Подразделение',
            'Сотрудник',
            'Часы (план)',
            'Часы (факт)',
            'Среднее время прихода',
            'Среднее время ухода',
        ]];

        $collection = $this->summary->getSummary(
            $request->get('employee'),
            $request->get('department'),
            $request->get('month'),
            $request->get('year'),
            $request->boolean('only_deviations')
        );

        foreach ($collection as $r) {
            $rows[] = [
                $r['month_year'],
                $r['department'],
                $r['employee_name'],
                ExcelExportService::formatHours($r['plan_hours']),
                ExcelExportService::formatHours($r['fact_hours']),
                $r['avg_in'],
                $r['avg_out'],
            ];
        }

        return [$rows, $this->makeFileName($request)];
    }

    /**
     * @param Request $request
     * @return string
     */
    private function makeFileName(Request $request): string
    {
        $parts = ['skud_summary'];

        if ($request->filled('department')) {
            $parts[] = 'dep_' . Str::slug($request->get('department'), '_');
        }

        if ($request->filled('employee')) {
            $parts[] = Str::slug(
                Employee::where('name', 'like', '%' . $request->employee . '%')
                    ->first()
                    ->name,
                '_'
            );
        }

        if ($request->filled('month') && $request->filled('year')) {
            $date = Carbon::create($request->get('year'), $request->get('month'));
            $parts[] = $date->format('m.Y');
        }

        if ($request->boolean('only_deviations')) {
            $parts[] = 'otkloneniya';
        }

        return implode('_', $parts) . '.xlsx';
    }
}
