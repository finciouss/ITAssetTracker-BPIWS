<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetAllocation;
use App\Exports\MonthlyReportExport;
use App\Helpers\ExcelTemplate;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function monthly(Request $request)
    {
        $month = $request->input('month', now()->format('m'));
        $year = $request->input('year', now()->format('Y'));
        
        $selectedDate = Carbon::createFromDate($year, $month, 1);

        $newAssets = Asset::whereMonth('purchase_date', $month)
                          ->whereYear('purchase_date', $year)
                          ->orderBy('purchase_date', 'desc')
                          ->get();

        $allocations = AssetAllocation::with(['asset', 'employee', 'project'])
                                      ->whereMonth('check_out_date', $month)
                                      ->whereYear('check_out_date', $year)
                                      ->orderBy('check_out_date', 'desc')
                                      ->get();

        $returns = AssetAllocation::with(['asset', 'employee', 'project'])
                                  ->whereMonth('actual_return_date', $month)
                                  ->whereYear('actual_return_date', $year)
                                  ->where('is_transfer_out', false)
                                  ->orderBy('actual_return_date', 'desc')
                                  ->get();

        $metrics = [
            'new_assets_count'  => $newAssets->count(),
            'allocations_count' => $allocations->count(),
            'returns_count'     => $returns->count(),
        ];

        return view('reports.monthly', compact(
            'month', 'year', 'selectedDate', 'newAssets', 'allocations', 'returns', 'metrics'
        ));
    }

    public function exportMonthlyExcel(Request $request)
    {
        $month = $request->input('month', now()->format('m'));
        $year  = $request->input('year',  now()->format('Y'));

        $dateStr = Carbon::createFromDate($year, $month, 1)->format('F_Y');

        return (new MonthlyReportExport($month, $year))
            ->download("Monthly_Asset_Report_{$dateStr}.xlsx");
    }
}
