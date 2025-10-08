<?php

namespace App\Http\Controllers;

use App\Imports\PlanHoursImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class UploadController extends Controller
{
    public function uploadExcel(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,xls']);
        $file = $request->file('file');
        Excel::import(new PlanHoursImport, $file);
        return back()->with('success', 'Плановые часы импортированы');
    }
}
