<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run()
    {
        $defaults = [
            // General
            ['key' => 'company_name', 'value' => 'Louis Marine Qatar', 'group' => 'general'],
            ['key' => 'company_address', 'value' => 'Doha, Qatar', 'group' => 'general'],
            ['key' => 'vat_number', 'value' => '', 'group' => 'general'],
            ['key' => 'company_logo', 'value' => '', 'group' => 'general'],
            
            // Email (Empty by default, uses env)
            ['key' => 'mail_host', 'value' => '', 'group' => 'email'],
            ['key' => 'mail_port', 'value' => '587', 'group' => 'email'],
            ['key' => 'mail_username', 'value' => '', 'group' => 'email'],
            ['key' => 'mail_password', 'value' => '', 'group' => 'email'],
            ['key' => 'mail_encryption', 'value' => 'tls', 'group' => 'email'],

            // Modules
            ['key' => 'cert_prefix', 'value' => 'LMQ', 'group' => 'modules'],
            ['key' => 'vat_percentage', 'value' => '5.00', 'group' => 'modules'],
        ];

        foreach ($defaults as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
