<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Models\CtaSection;
use Illuminate\Http\Request;

class CtaSectionController extends Controller
{
    public function index()
    {
        $ctaSections = CtaSection::all();
        return view('admin.cms.landing-page.cta-sections.index', compact('ctaSections'));
    }

    public function create()
    {
        return view('admin.cms.landing-page.cta-sections.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'section_key' => 'required|string|max:100|unique:cms_cta_sections,section_key',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|string|max:255',
            'background_color' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        CtaSection::create($validated);

        return redirect()->route('admin.cms.landing-page.cta-sections.index')
            ->with('success', 'CTA Section created successfully.');
    }

    public function edit(CtaSection $ctaSection)
    {
        return view('admin.cms.landing-page.cta-sections.edit', compact('ctaSection'));
    }

    public function update(Request $request, CtaSection $ctaSection)
    {
        $validated = $request->validate([
            'section_key' => 'required|string|max:100|unique:cms_cta_sections,section_key,' . $ctaSection->id,
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|string|max:255',
            'background_color' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $ctaSection->update($validated);

        return redirect()->route('admin.cms.landing-page.cta-sections.index')
            ->with('success', 'CTA Section updated successfully.');
    }

    public function destroy(CtaSection $ctaSection)
    {
        $ctaSection->delete();

        return redirect()->route('admin.cms.landing-page.cta-sections.index')
            ->with('success', 'CTA Section deleted successfully.');
    }
}
