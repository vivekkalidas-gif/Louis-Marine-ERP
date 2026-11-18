<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Models\CmsCertification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificationController extends Controller
{
    public function index()
    {
        $certifications = CmsCertification::orderBy('order')->paginate(10);
        return view('admin.cms.certifications.index', compact('certifications'));
    }

    public function create()
    {
        return view('admin.cms.certifications.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'issuing_body' => 'nullable',
            'image_path' => 'nullable|image|max:2048',
            'order' => 'integer',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image_path')) {
            $validated['image_path'] = $request->file('image_path')->store('cms/certifications', 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        CmsCertification::create($validated);
        return redirect()->route('admin.cms.certifications.index')->with('success', 'Certification added successfully.');
    }

    public function edit(CmsCertification $certification)
    {
        return view('admin.cms.certifications.edit', compact('certification'));
    }

    public function update(Request $request, CmsCertification $certification)
    {
        $validated = $request->validate([
            'name' => 'required',
            'issuing_body' => 'nullable',
            'image_path' => 'nullable|image|max:2048',
            'order' => 'integer',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image_path')) {
            if ($certification->image_path) {
                Storage::disk('public')->delete($certification->image_path);
            }
            $validated['image_path'] = $request->file('image_path')->store('cms/certifications', 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        $certification->update($validated);
        return redirect()->route('admin.cms.certifications.index')->with('success', 'Certification updated successfully.');
    }

    public function destroy(CmsCertification $certification)
    {
        if ($certification->image_path) {
            Storage::disk('public')->delete($certification->image_path);
        }
        $certification->delete();
        return redirect()->route('admin.cms.certifications.index')->with('success', 'Certification removed.');
    }
}
