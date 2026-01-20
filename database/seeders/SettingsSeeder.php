<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            // Company Profile
            ['key' => 'company_name', 'value' => 'Louis Marine Qatar', 'group' => 'company'],
            ['key' => 'company_email', 'value' => 'info@louismarine.qa', 'group' => 'company'],
            ['key' => 'company_phone', 'value' => '+974 4444 8888', 'group' => 'company'],
            ['key' => 'company_address', 'value' => 'Unit 42, Marine Industrial Park, Doha', 'group' => 'company'],
            ['key' => 'company_website', 'value' => 'https://louismarine.qa', 'group' => 'company'],
            
            // Branding
            ['key' => 'branding_primary_color', 'value' => '#1e3a8a', 'group' => 'branding'], // Navy Blue
            ['key' => 'branding_secondary_color', 'value' => '#64748b', 'group' => 'branding'], // Slate
            ['key' => 'site_favicon', 'value' => null, 'group' => 'branding'],
            
            // Email
            ['key' => 'mail_from_name', 'value' => 'Louis Marine Service', 'group' => 'email'],
            ['key' => 'mail_from_address', 'value' => 'noreply@louismarine.qa', 'group' => 'email'],
            ['key' => 'mail_notify_expiry', 'value' => '1', 'group' => 'email'],
            
            // Numbering
            ['key' => 'prefix_invoice', 'value' => 'INV', 'group' => 'numbering'],
            ['key' => 'prefix_quotation', 'value' => 'QTE', 'group' => 'numbering'],
            ['key' => 'prefix_job', 'value' => 'JOB', 'group' => 'numbering'],
            ['key' => 'prefix_certificate', 'value' => 'CERT', 'group' => 'numbering'],
            
            // Security
            ['key' => 'security_session_lifetime', 'value' => '120', 'group' => 'security'],
            ['key' => 'security_maintenance_mode', 'value' => '0', 'group' => 'security'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
