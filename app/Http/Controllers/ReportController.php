<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Services\ExcelExportService;
use App\Services\ReportExportService;
use App\Services\SummaryReportExportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function summary(Request $request)
    {
        return view('reports.summary');
    }

    public function detail($employeeId, Request $request)
    {
        $employee = Employee::find($employeeId);
        return view('reports.detail', compact('employee'));
    }

    public function export(Request $request, ReportExportService $reportExport, ExcelExportService $excel)
    {
        [$rows, $file] = $reportExport->prepare($request);
        $path = $excel->export($rows, $file);

        return response()->download($path)->deleteFileAfterSend();
    }

    public function exportSummary(Request $request, SummaryReportExportService $reportExport, ExcelExportService $excel)
    {
        [$rows, $filename] = $reportExport->prepare($request);
        $path = $excel->exportSimple($rows, $filename);

        return response()->download($path)->deleteFileAfterSend();
    }
}
