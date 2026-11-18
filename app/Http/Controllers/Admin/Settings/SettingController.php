<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->groupBy('group');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'company_logo' => 'nullable|file|mimes:png,jpg,jpeg,svg,webp|max:10240',
            'site_favicon' => 'nullable|file|mimes:png,jpg,jpeg,ico,svg|max:5120',
            'certificate_header_png' => 'nullable|file|mimes:png,jpg,jpeg,webp|max:20480',
            'certificate_footer_png' => 'nullable|file|mimes:png,jpg,jpeg,webp|max:20480',
        ]);

        // Handle File Uploads
        if ($request->hasFile('company_logo')) {
            $path = $request->file('company_logo')->store('uploads', 'public');
            Setting::updateOrCreate(['key' => 'company_logo'], ['value' => $path, 'group' => 'branding']);
        }
        
        if ($request->hasFile('site_favicon')) {
            $path = $request->file('site_favicon')->store('uploads', 'public');
            Setting::updateOrCreate(['key' => 'site_favicon'], ['value' => $path, 'group' => 'branding']);
        }

        if ($request->hasFile('certificate_header_png')) {
            $path = $request->file('certificate_header_png')->store('uploads/certificates', 'public');
            Setting::updateOrCreate(['key' => 'certificate_header_png'], ['value' => $path, 'group' => 'branding']);
        }

        if ($request->hasFile('certificate_footer_png')) {
            $path = $request->file('certificate_footer_png')->store('uploads/certificates', 'public');
            Setting::updateOrCreate(['key' => 'certificate_footer_png'], ['value' => $path, 'group' => 'branding']);
        }

        // Remove Certificate Header / Footer if requested
        if ($request->boolean('remove_certificate_header_png')) {
            $headerSetting = Setting::where('key', 'certificate_header_png')->first();
            if ($headerSetting && !empty($headerSetting->value)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($headerSetting->value);
            }
            Setting::where('key', 'certificate_header_png')->delete();
        }

        if ($request->boolean('remove_certificate_footer_png')) {
            $footerSetting = Setting::where('key', 'certificate_footer_png')->delete();
            if ($footerSetting && !empty($footerSetting->value)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($footerSetting->value);
            }
            Setting::where('key', 'certificate_footer_png')->delete();
        }

        // Handle other inputs
        $ignore = ['_token', 'company_logo', 'site_favicon', 'certificate_header_png', 'certificate_footer_png', 'remove_certificate_header_png', 'remove_certificate_footer_png'];
        
        foreach ($request->except($ignore) as $key => $value) {
            $group = 'general';
            if (str_starts_with($key, 'company_')) $group = 'company';
            elseif (str_starts_with($key, 'branding_') || str_starts_with($key, 'certificate_')) $group = 'branding';
            elseif (str_starts_with($key, 'mail_')) $group = 'email';
            elseif (str_starts_with($key, 'prefix_')) $group = 'numbering';
            elseif (str_starts_with($key, 'security_')) $group = 'security';

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => $group]
            );
        }

        \Illuminate\Support\Facades\Cache::forget('app_settings');

        return back()->with('success', 'System settings & branding overlays updated successfully.');
    }
}
