<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Models\CmsPartner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PartnerController extends Controller
{
    public function index()
    {
        $partners = CmsPartner::orderBy('order')->paginate(10);
        return view('admin.cms.partners.index', compact('partners'));
    }

    public function create()
    {
        return view('admin.cms.partners.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'website_url' => 'nullable|url',
            'logo_path' => 'nullable|image|max:2048',
            'order' => 'integer',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('logo_path')) {
            $validated['logo_path'] = $request->file('logo_path')->store('cms/partners', 'public');
        }

        CmsPartner::create($validated);
        return redirect()->route('admin.cms.partners.index')->with('success', 'Partner added successfully.');
    }

    public function edit(CmsPartner $partner)
    {
        return view('admin.cms.partners.edit', compact('partner'));
    }

    public function update(Request $request, CmsPartner $partner)
    {
        $validated = $request->validate([
            'name' => 'required',
            'website_url' => 'nullable|url',
            'logo_path' => 'nullable|image|max:2048',
            'order' => 'integer',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('logo_path')) {
            // Delete old image if exists
            if ($partner->logo_path) {
                Storage::disk('public')->delete($partner->logo_path);
            }
            $validated['logo_path'] = $request->file('logo_path')->store('cms/partners', 'public');
        }

        // Handle checkbox
        $validated['is_active'] = $request->has('is_active');

        $partner->update($validated);
        return redirect()->route('admin.cms.partners.index')->with('success', 'Partner updated successfully.');
    }

    public function destroy(CmsPartner $partner)
    {
        if ($partner->logo_path) {
            Storage::disk('public')->delete($partner->logo_path);
        }
        $partner->delete();
        return redirect()->route('admin.cms.partners.index')->with('success', 'Partner removed.');
    }
}
