<?php

// app/Http/Controllers/DashboardExportController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\IndicatorsExport;

class DashboardExportController extends Controller
{
    public function export(Request $request)
    {
        //   dd($request->all());
        $filters = $request->only([
            'year',
            'standard_id',
            'category_id',
            'category',       // support category name grouping
            'category_name',  // alias
            'status',
            'dept_id',
            'code'
        ]);


        $fileName = 'dashboard_data_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new IndicatorsExport($filters), $fileName);
    }
}
