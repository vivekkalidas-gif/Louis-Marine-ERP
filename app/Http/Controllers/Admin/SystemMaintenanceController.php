<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class SystemMaintenanceController extends Controller
{
    /**
     * Show the maintenance dashboard.
     */
    public function index()
    {
        // Only allow admins
        if (!auth()->user() || !auth()->user()->hasRole('admin')) {
             abort(403, 'Unauthorized');
        }

        return view('admin.maintenance.index');
    }

    /**
     * Run the Accounting Seeder to fix Chart of Accounts.
     */
    public function runAccountingSeeder()
    {
        if (!auth()->user() || !auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized');
        }

        try {
            // Run the specific seeder
            Artisan::call('db:seed', [
                '--class' => 'AccountingSeeder',
                '--force' => true // Required for production
            ]);
            
            $output = Artisan::output();
            Log::info('Accounting Seeder Run via Web: ' . $output);

            return back()->with('success', 'Accounting System Initialized Successfully. You can now finalize invoices.');
        } catch (\Exception $e) {
            Log::error('Seeder Failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to run seeder: ' . $e->getMessage());
        }
    }

    /**
     * Clear System Caches
     */
    public function clearCache()
    {
        if (!auth()->user() || !auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized');
        }

        try {
            Artisan::call('optimize:clear');
            return back()->with('success', 'System caches (Route, View, Config) cleared successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to clear cache: ' . $e->getMessage());
        }
    }
}
