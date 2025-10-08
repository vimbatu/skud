<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelExportService
{
    public function export(array $rows, string $filename): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // заливаем данные
        $sheet->fromArray($rows, null, 'A1');

        // стили для заголовка
        $lastCol = $sheet->getHighestColumn();
        $sheet->getStyle("A1:{$lastCol}1")->getFont()->setBold(true);
        $sheet->getStyle("A1:{$lastCol}1")->getAlignment()->setHorizontal('center');

        // автоширина колонок
        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // колонки: C = Приход, D = Уход, E = Часы (факт), G = Отклонение
        $rowCount = count($rows);
        for ($i = 2; $i <= $rowCount; $i++) {
            $deviation = $sheet->getCell("H{$i}")->getValue(); // 👈 теперь читаем колонку H

            // Опоздал
            $this->colorCell($sheet, "C{$i}",
                $deviation && str_contains($deviation, 'опоздал')
            );

            // Слинял
            $this->colorCell($sheet, "D{$i}",
                $deviation && str_contains($deviation, 'слинял')
            );

            // Отработал мало
            $this->colorCell($sheet, "E{$i}",
                $deviation && (str_contains($deviation, 'откосил') || str_contains($deviation, 'недоработал'))
            );

            // Подсветка отклонения по времени (F)
            $diff = $sheet->getCell("F{$i}")->getValue();
            $this->colorCell($sheet, "F{$i}", str_starts_with($diff, '-'));
        }

        $writer = new Xlsx($spreadsheet);
        $path = storage_path('app/public/' . $filename);

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $writer->save($path);

        return $path;
    }

    private function colorCell($sheet, string $cell, bool $isDeviation): void
    {
        $sheet->getStyle($cell)->getFont()->getColor()->setARGB(
            $isDeviation ? 'FFFF0000' : 'FF008000' // красный если отклонение, зелёный если норма
        );
    }

    private static function toSeconds(?string $time): int
    {
        if (!$time || !preg_match('/^(\d+):(\d+):(\d+)$/', $time, $m)) {
            return 0;
        }
        return ($m[1] * 3600 + $m[2] * 60 + $m[3]);
    }

    public static function deviationTime(?string $worked, float $planHours): string
    {
        $workedSecs = self::toSeconds($worked);
        $planSecs   = $planHours * 3600;

        $diffSecs = $workedSecs - $planSecs;
        $sign = $diffSecs >= 0 ? '+' : '-';

        return $sign . gmdate('H:i:s', abs($diffSecs));
    }

    public static function formatHours(?float $hours): ?string
    {
        if ($hours === null) {
            return null;
        }

        $h = floor($hours);
        $m = round(($hours - $h) * 60);

        return sprintf('%02d:%02d', $h, $m);
    }
}
