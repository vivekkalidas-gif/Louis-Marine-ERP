<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class InstallerController extends Controller
{
    public function show()
    {
        // Check if already installed (simple check if users table exists and has data)
        try {
             if (DB::table('users')->exists()) {
                 return redirect('/login')->with('status', 'System already installed.');
             }
        } catch (\Exception $e) {
            // DB not ready, proceed to install
        }

        return view('system.install');
    }

    public function run()
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Artisan::call('migrate:fresh', ['--force' => true]);
            Artisan::call('db:seed', ['--force' => true]);
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            return redirect('/login')->with('success', 'System installed successfully! Login with admin@louis-marine.com / password');
        } catch (\Exception $e) {
            return back()->with('error', 'Installation failed: ' . $e->getMessage());
        }
    }
}
