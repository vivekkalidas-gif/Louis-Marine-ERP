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
        // Handle File Uploads first
        if ($request->hasFile('company_logo')) {
            $path = $request->file('company_logo')->store('uploads', 'public');
            Setting::updateOrCreate(['key' => 'company_logo'], ['value' => 'storage/' . $path, 'group' => 'branding']);
        }
        
        if ($request->hasFile('site_favicon')) {
            $path = $request->file('site_favicon')->store('uploads', 'public');
            Setting::updateOrCreate(['key' => 'site_favicon'], ['value' => 'storage/' . $path, 'group' => 'branding']);
        }

        // Handle other inputs
        $ignore = ['_token', 'company_logo', 'site_favicon'];
        
        foreach ($request->except($ignore) as $key => $value) {
            // Find existing to allow preserving group if needed, or default to 'general'
            $existing = Setting::where('key', $key)->first();
            $group = $existing ? $existing->group : 'general';
            
            // Detect group from prefix if new
            if (!$existing) {
                if (str_starts_with($key, 'company_')) $group = 'company';
                elseif (str_starts_with($key, 'branding_')) $group = 'branding';
                elseif (str_starts_with($key, 'mail_')) $group = 'email';
                elseif (str_starts_with($key, 'prefix_')) $group = 'numbering';
                elseif (str_starts_with($key, 'security_')) $group = 'security';
            }

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => $group]
            );
        }

        return back()->with('success', 'System settings updated successfully.');
    }
}
