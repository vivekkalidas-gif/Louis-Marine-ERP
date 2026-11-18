<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use App\Listeners\LogSuccessfulLogin;
use App\Listeners\LogSuccessfulLogout;
use Illuminate\Database\Eloquent\Model;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::shouldBeStrict(!app()->isProduction());

        // System Owner Bypass (Technical God Mode)
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            if ($user->hasRole('system-owner')) {
                return true;
            }
        });

        // Load Global Settings
        // Load Global Settings
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $settings = \App\Models\Setting::getAllCached();
                view()->share('globalSettings', $settings);
                
                // Hydrate config for global access (e.g. config('settings.company_name'))
                config(['settings' => $settings->toArray()]);

                if (!empty($settings['mail_host'])) {
                    config([
                        'mail.mailers.smtp.host' => $settings['mail_host'],
                        'mail.mailers.smtp.port' => $settings['mail_port'],
                        'mail.mailers.smtp.username' => $settings['mail_username'],
                        'mail.mailers.smtp.password' => $settings['mail_password'],
                        'mail.mailers.smtp.encryption' => $settings['mail_encryption'],
                    ]);
                }

                // Hydrate Google Drive Config (DEBUGGING ENABLED)
                \Illuminate\Support\Facades\Log::info('AppServiceProvider: Hydrating Google Drive Config');
                \Illuminate\Support\Facades\Log::info('Settings Found:', [
                    'client_id_exists' => isset($settings['google_drive_client_id']),
                    'refresh_token_exists' => isset($settings['google_drive_refresh_token']),
                    'refresh_token_preview' => substr($settings['google_drive_refresh_token'] ?? '', 0, 5) . '...' // Log only start for security
                ]);

                if (!empty($settings['google_drive_refresh_token'])) {
                    config([
                        'filesystems.disks.google.clientId' => $settings['google_drive_client_id'] ?? '',
                        'filesystems.disks.google.clientSecret' => $settings['google_drive_client_secret'] ?? '',
                        'filesystems.disks.google.refreshToken' => $settings['google_drive_refresh_token'],
                        'filesystems.disks.google.folder' => $settings['google_drive_folder_id'] ?? '/',
                    ]);
                    \Illuminate\Support\Facades\Log::info('AppServiceProvider: Config updated successfully.');
                } else {
                    \Illuminate\Support\Facades\Log::warning('AppServiceProvider: Refresh Token is empty or missing in settings.');
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('AppServiceProvider Error: ' . $e->getMessage());
            // Database not ready or connection failed. 
            // Silently fail so the installer can run.
        }

        // Login Logging
        Event::listen(Login::class, function ($event) {
            \App\Models\LoginHistory::create([
                'user_id' => $event->user->id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'status' => 'success',
                'login_at' => now(),
            ]);
        });
        
        // Failed Login Logging
        Event::listen(\Illuminate\Auth\Events\Failed::class, function ($event) {
            $user = null;
            if ($event->user) {
                $user = $event->user;
            } elseif (isset($event->credentials['email'])) {
                $user = \App\Models\User::where('email', $event->credentials['email'])->first();
            }

            \App\Models\LoginHistory::create([
                'user_id' => $user ? $user->id : null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'status' => 'failed',
                'login_at' => now(),
            ]);
        });
        
        // Register Google Drive Driver
        \Illuminate\Support\Facades\Storage::extend('google', function($app, $config) {
            $options = [];
            if (!empty($config['teamDriveId'] ?? null)) {
                $options['teamDriveId'] = $config['teamDriveId'];
            }
        
            // Fail-safe: Fetch from DB if config is missing (JIT Configuration)
            $clientId = $config['clientId'];
            $clientSecret = $config['clientSecret'];
            $refreshToken = $config['refreshToken'];

            if (empty($refreshToken)) {
                // Try to get directly from DB
                try {
                     $dbSettings = \App\Models\Setting::whereIn('key', [
                        'google_drive_client_id', 
                        'google_drive_client_secret', 
                        'google_drive_refresh_token',
                        'google_drive_folder_id'
                    ])->pluck('value', 'key');

                    $clientId = $dbSettings['google_drive_client_id'] ?? $clientId;
                    $clientSecret = $dbSettings['google_drive_client_secret'] ?? $clientSecret;
                    $refreshToken = $dbSettings['google_drive_refresh_token'] ?? $refreshToken;
                    $config['folder'] = $dbSettings['google_drive_folder_id'] ?? ($config['folder'] ?? '/');

                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Google Driver JIT Config Failed: ' . $e->getMessage());
                }
            }

            if (empty($clientId) || empty($clientSecret) || empty($refreshToken)) {
                throw new \Exception("GOOGLE DRIVE SETUP ERROR: Missing Credentials! 
                Please go to Admin -> Settings -> Cloud Backups and ensure all fields are saved.
                Debug Info:
                Client ID: " . ($clientId ? 'SET' : 'MISSING') . "
                Client Secret: " . ($clientSecret ? 'SET' : 'MISSING') . "
                Refresh Token: " . ($refreshToken ? 'SET' : 'MISSING') . "
                DB Lookup: " . (isset($dbSettings) ? 'Attempted' : 'Not Attempted') . "
                ");
            }

            $client = new \Google\Client();
            $client->setClientId($clientId);
            $client->setClientSecret($clientSecret);
            $client->refreshToken($refreshToken);
            $client->addScope(\Google\Service\Drive::DRIVE);
            
            $service = new \Google\Service\Drive($client);
            $adapter = new \Masbug\Flysystem\GoogleDriveAdapter($service, $config['folder'] ?? '/', $options);
            $driver = new \League\Flysystem\Filesystem($adapter);
        
            return new \Illuminate\Filesystem\FilesystemAdapter($driver, $adapter);
        });
    }
}
