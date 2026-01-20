<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Super Admin - CMS Manager Dashboard
        if ($user->hasRole('super-admin')) {
            return redirect()->route('admin.cms.dashboard');
        }

        // Store Keeper
        if ($user->hasRole('storekeeper')) {
            return redirect()->route('storekeeper.dashboard');
        }

        // Admin & Manager (Operational Dashboard)
        if ($user->hasRole('admin') || $user->hasRole('manager')) {
            $stats = [];

            // Users (Admin/Super Admin only usually, or Manager)
            if ($user->can('users.view')) {
                 $stats[] = ['title' => 'Total Users', 'value' => \App\Models\User::count(), 'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>', 'trend' => 12];
            }

            // Jobs (Manager/Admin only)
            if ($user->can('jobs.view')) {
                $stats[] = ['title' => 'Active Jobs', 'value' => \App\Models\ServiceJob::whereIn('status', ['Assigned', 'In Progress'])->count(), 'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>', 'trend' => 5];
            }

            // Invoices/Finance
            if ($user->can('invoices.view')) { // Assuming permission exists or use accounts.view
                $stats[] = ['title' => 'Pending Invoices', 'value' => 0, 'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>', 'trend' => -2, 'color' => 'yellow'];
            }

            // Enquiries (CMS/Super Admin)
            if ($user->can('enquiries.view')) {
                $stats[] = ['title' => 'New CMS Enquiries', 'value' => \App\Models\ContactSubmission::where('status', 'new')->count(), 'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>', 'color' => 'blue'];
            }

            // Backups/System (Super Admin)
            if ($user->can('backups.manage')) {
                 $stats[] = ['title' => 'System Alerts', 'value' => 3, 'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>', 'color' => 'red'];
            }

            $data = [
                'stats' => $stats,
                'notifications' => [], // Populate dynamically if needed
                'expiring' => [] // Populate dynamically
            ];

            return view('dashboard.admin', $data);
        }

        if ($user->hasRole('cms-manager')) {
            return redirect()->route('admin.cms.services.index');
        }

        if ($user->hasRole('engineer')) {
            return redirect()->route('engineer.dashboard');
        }

        if ($user->hasRole('customer')) {
            return view('dashboard.customer');
        }

        if ($user->hasRole('accounts')) {
            return redirect()->route('admin.finance.index');
        }

        // Default view for others (Stockist, PRO)
        return view('dashboard.admin', [
            'stats' => [],
            'notifications' => [],
            'expiring' => []
        ]);
    }
}
