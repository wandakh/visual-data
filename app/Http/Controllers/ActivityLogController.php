<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $showAll = $request->boolean('show_all');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if (!$showAll && !$startDate && !$endDate) {
            $startDate = $endDate = today()->toDateString();
        }

        $applyDateFilter = function ($query) use ($startDate, $endDate) {
            if ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('created_at', '<=', $endDate);
            }
        };

        $dataLogsQuery = ActivityLog::with('user')->whereIn('action', ['created', 'updated', 'deleted', 'restored']);
        $applyDateFilter($dataLogsQuery);
        $dataLogs = $dataLogsQuery->latest('created_at')->paginate(10, ['*'], 'data_page')->withQueryString();

        $importLogsQuery = ActivityLog::with('user')->where('action', 'imported');
        $applyDateFilter($importLogsQuery);
        $importLogs = $importLogsQuery->latest('created_at')->paginate(10, ['*'], 'import_page')->withQueryString();

        $exportLogsQuery = ActivityLog::with('user')->where('action', 'exported');
        $applyDateFilter($exportLogsQuery);
        $exportLogs = $exportLogsQuery->latest('created_at')->paginate(10, ['*'], 'export_page')->withQueryString();

        return view('activity-log.index', [
            'dataLogs' => $dataLogs,
            'importLogs' => $importLogs,
            'exportLogs' => $exportLogs,
            'title' => 'Jejak Audit',
            'showAll' => $showAll,
        ]);
    }
}
