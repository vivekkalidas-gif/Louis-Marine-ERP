<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

use App\Models\Setting;
use Illuminate\Support\Facades\Artisan;

class BackupController extends Controller
{
    private function getBackupPath()
    {
        return config('backup.backup.name');
    }

    public function index()
    {
        $backupPath = $this->getBackupPath();

        // Ensure directory exists
        if (!Storage::exists($backupPath)) {
            Storage::makeDirectory($backupPath);
        }

        $files = Storage::files($backupPath);
        $backups = [];
        
        foreach ($files as $file) {
            // Filter only zip files
            if (pathinfo($file, PATHINFO_EXTENSION) !== 'zip') continue;

            $backups[] = [
                'name' => basename($file),
                'size' => $this->formatSize(Storage::size($file)),
                'date' => \Carbon\Carbon::createFromTimestamp(Storage::lastModified($file)),
                'path' => $file
            ];
        }

        // Sort by date desc
        usort($backups, fn($a, $b) => $b['date'] <=> $a['date']);

        return view('admin.backups.index', compact('backups'));
    }

    public function store(Request $request)
    {
        // Increase memory and time priority
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        try {
            // Configure mysqldump path dynamically for Windows/XAMPP
            // This is required for Spatie DB Dumper to work
            if (file_exists('D:/xampp/mysql/bin/mysqldump.exe')) {
                config(['database.connections.mysql.dump.dump_binary_path' => 'D:/xampp/mysql/bin']);
            } elseif (file_exists('C:/xampp/mysql/bin/mysqldump.exe')) {
                config(['database.connections.mysql.dump.dump_binary_path' => 'C:/xampp/mysql/bin']);
            }

            // Run the backup command
            $params = ['--disable-notifications' => true];
            
            // If user wants to download, we only need a local backup (ignoring Google Drive for speed/errors)
            if ($request->has('download')) {
                $params['--only-to-disk'] = 'local';
            }

            Artisan::call('backup:run', $params);
            
            // Check output for errors
            $output = Artisan::output();
            if (str_contains($output, 'Backup failed')) {
                return back()->with('error', 'Backup process failed. Log: ' . $output);
            }

            // Handle Download
            if ($request->has('download')) {
                $backupPath = $this->getBackupPath();
                $files = Storage::files($backupPath);
                
                // Find latest file
                $latestFile = null;
                $latestTime = 0;
                
                foreach ($files as $file) {
                    if (pathinfo($file, PATHINFO_EXTENSION) !== 'zip') continue;
                    $time = Storage::lastModified($file);
                    if ($time > $latestTime) {
                        $latestTime = $time;
                        $latestFile = $file;
                    }
                }

                if ($latestFile) {
                    return Storage::download($latestFile);
                }
            }

            return back()->with('success', 'Backup created successfully (Local + Cloud).');

        } catch (\Exception $e) {
            return back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'google_drive_client_id' => 'nullable|string',
            'google_drive_client_secret' => 'nullable|string',
            'google_drive_refresh_token' => 'nullable|string',
            'google_drive_folder_id' => 'nullable|string',
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => 'backup', 'type' => 'string']
            );
        }

        // Clear config cache to ensure next request picks up new values via AppServiceProvider
        Artisan::call('config:clear');

        return back()->with('success', 'Backup settings updated successfully.');
    }

    public function download($filename)
    {
        $path = $this->getBackupPath() . '/' . $filename;
        if (Storage::exists($path)) {
            return Storage::download($path);
        }
        return back()->with('error', 'File not found.');
    }

    public function destroy($filename)
    {
        $path = $this->getBackupPath() . '/' . $filename;
        if (Storage::exists($path)) {
            Storage::delete($path);
            return back()->with('success', 'Backup deleted.');
        }
        return back()->with('error', 'File not found.');
    }

    private function formatSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        for ($i = 0; $bytes > 1024; $i++) $bytes /= 1024;
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
