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
        
            $client = new \Google\Client();
            $client->setClientId($config['clientId']);
            $client->setClientSecret($config['clientSecret']);
            $client->refreshToken($config['refreshToken']);
            
            $service = new \Google\Service\Drive($client);
            $adapter = new \Masbug\Flysystem\GoogleDriveAdapter($service, $config['folder'] ?? '/', $options);
            $driver = new \League\Flysystem\Filesystem($adapter);
        
            return new \Illuminate\Filesystem\FilesystemAdapter($driver, $adapter);
        });
    }
}
