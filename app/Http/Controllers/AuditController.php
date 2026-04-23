<?php

namespace App\Http\Controllers;

use App\Models\AssetLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index()
    {
        $assetLogs = DB::table('asset_logs')
            ->leftJoin('assets', 'asset_logs.asset_id', '=', 'assets.id')
            ->select([
                'asset_logs.action_date as timestamp',
                'asset_logs.user_name as user',
                'asset_logs.action_type',
                DB::raw("'asset' as log_type"),
                'asset_logs.asset_id as entity_id',
                'assets.tag_number as asset_tag',
                'assets.name as asset_name',
                'asset_logs.description as description'
            ]);

        $systemLogs = DB::table('system_logs')
            ->select([
                'timestamp',
                'user_email as user',
                'action_type',
                DB::raw("'system' as log_type"),
                DB::raw("NULL as entity_id"),
                DB::raw("NULL as asset_tag"),
                DB::raw("NULL as asset_name"),
                'description'
            ]);

        $logs = $systemLogs->unionAll($assetLogs)
            ->orderBy('timestamp', 'desc')
            ->paginate(50);

        return view('users.audit', compact('logs'));
    }
}
