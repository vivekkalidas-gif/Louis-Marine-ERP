<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use PDO;

class InstallerController extends Controller
{
    public function show()
    {
        try {
             if (DB::connection()->getPdo() && DB::table('users')->exists()) {
                 return redirect('/login')->with('status', 'System already installed.');
             }
        } catch (\Exception $e) {
            // DB not ready or not installed, proceed to install page
        }

        return view('system.install', [
            'db_host' => env('DB_HOST', '127.0.0.1'),
            'db_port' => env('DB_PORT', '3306'),
            'db_database' => env('DB_DATABASE', 'louis_local'),
            'db_username' => env('DB_USERNAME', 'root'),
            'db_password' => env('DB_PASSWORD', ''),
        ]);
    }

    public function run(Request $request)
    {
        $request->validate([
            'db_host' => 'required|string',
            'db_port' => 'required|string',
            'db_database' => 'required|string',
            'db_username' => 'required|string',
            'db_password' => 'nullable|string',
        ]);

        $host = $request->input('db_host');
        $port = $request->input('db_port');
        $database = $request->input('db_database');
        $username = $request->input('db_username');
        $password = $request->input('db_password', '');

        // 1. Test database connection & create database if it doesn't exist
        try {
            $pdo = new PDO("mysql:host={$host};port={$port}", $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Could not connect to MySQL server: ' . $e->getMessage());
        }

        // 2. Update .env file
        $this->updateEnv([
            'DB_CONNECTION' => 'mysql',
            'DB_HOST' => $host,
            'DB_PORT' => $port,
            'DB_DATABASE' => $database,
            'DB_USERNAME' => $username,
            'DB_PASSWORD' => $password ?? '',
            'SESSION_DRIVER' => 'file',
            'APP_URL' => url('/'),
        ]);

        // Reconfigure active Laravel DB connection
        Config::set('database.connections.mysql.host', $host);
        Config::set('database.connections.mysql.port', $port);
        Config::set('database.connections.mysql.database', $database);
        Config::set('database.connections.mysql.username', $username);
        Config::set('database.connections.mysql.password', $password);
        DB::purge('mysql');
        DB::reconnect('mysql');

        // 3. Run migrations and seeders
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Artisan::call('migrate:fresh', ['--force' => true]);
            Artisan::call('db:seed', ['--force' => true]);
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            return redirect('/login')->with('success', 'System installed successfully! Login with admin@louis-marine.com / password');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Installation failed during migration: ' . $e->getMessage());
        }
    }

    private function updateEnv(array $data)
    {
        $envFile = base_path('.env');
        if (!file_exists($envFile)) {
            return;
        }

        $content = file_get_contents($envFile);

        foreach ($data as $key => $value) {
            $pattern = "/^{$key}=.*/m";
            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, "{$key}={$value}", $content);
            } else {
                $content .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envFile, $content);
    }
}
