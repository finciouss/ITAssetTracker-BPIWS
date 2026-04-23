<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Project;
use App\Models\SystemLog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalAssets = Asset::count();
        $activeProjects = Project::where('status', Project::STATUS_ONGOING)->count();
        $inStockAssets = Asset::where('status', Asset::STATUS_IN_STOCK)->count();
        
        $overdueAllocations = \App\Models\AssetAllocation::whereNull('actual_return_date')
            ->whereNotNull('expected_return_date')
            ->where('expected_return_date', '<', now()->startOfDay())
            ->count();

        $recentLogs = \App\Models\AssetLog::with('asset')->orderBy('action_date', 'desc')->take(5)->get();

        return view('dashboard', compact('totalAssets', 'activeProjects', 'inStockAssets', 'recentLogs', 'overdueAllocations'));
    }
}
