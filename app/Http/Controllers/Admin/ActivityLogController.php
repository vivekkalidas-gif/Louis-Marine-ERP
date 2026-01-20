<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of activity logs
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        // Filter by user
        if ($request->filled('user_id')) {
            $query->byUser($request->user_id);
        }

        // Filter by module
        if ($request->filled('module')) {
            $query->module($request->module);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->action($request->action);
        }

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->dateRange($request->start_date, $request->end_date);
        }

        // Filter by model type
        if ($request->filled('model_type')) {
            $query->modelType($request->model_type);
        }

        $logs = $query->paginate(50);

        // Get unique modules for filter dropdown
        $modules = ActivityLog::distinct()->pluck('module')->sort();
        
        // Get unique model types for filter dropdown
        $modelTypes = ActivityLog::distinct()->pluck('model_type')->map(function($type) {
            return class_basename($type);
        })->sort();

        // Get users for filter dropdown
        $users = User::orderBy('name')->get();

        return view('admin.activity-logs.index', compact('logs', 'modules', 'modelTypes', 'users'));
    }

    /**
     * Display the specified activity log
     */
    public function show($id)
    {
        $log = ActivityLog::with('user')->findOrFail($id);

        return view('admin.activity-logs.show', compact('log'));
    }
}
