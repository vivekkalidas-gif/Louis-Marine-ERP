<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use App\Models\PageSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PageSectionController extends Controller
{
    public function index(CmsPage $page)
    {
        $sections = $page->sections()->orderBy('order')->get();
        return view('admin.cms.pages.sections.index', compact('page', 'sections'));
    }

    public function create(CmsPage $page)
    {
        return view('admin.cms.pages.sections.create', compact('page'));
    }

    public function store(Request $request, CmsPage $page)
    {
        $validated = $request->validate([
            'title' => 'nullable|string',
            'subtitle' => 'nullable|string',
            'content' => 'nullable|string',
            'image_path' => 'nullable|image|max:2048',
            'order' => 'integer',
            'section_key' => 'nullable|string',
            'is_visible' => 'boolean',
        ]);

        if ($request->hasFile('image_path')) {
            $validated['image_path'] = $request->file('image_path')->store('cms/sections', 'public');
        }
        
        // Defaults
        $validated['is_visible'] = $request->has('is_visible');

        $page->sections()->create($validated);

        return redirect()->route('admin.cms.pages.sections.index', $page)->with('success', 'Section added successfully.');
    }

    public function edit(CmsPage $page, PageSection $section)
    {
        return view('admin.cms.pages.sections.edit', compact('page', 'section'));
    }

    public function update(Request $request, CmsPage $page, PageSection $section)
    {
        $validated = $request->validate([
            'title' => 'nullable|string',
            'subtitle' => 'nullable|string',
            'content' => 'nullable|string',
            'image_path' => 'nullable|image|max:2048',
            'order' => 'integer',
            'section_key' => 'nullable|string',
            'is_visible' => 'boolean',
        ]);

        if ($request->hasFile('image_path')) {
            if ($section->image_path) {
                Storage::disk('public')->delete($section->image_path);
            }
            $validated['image_path'] = $request->file('image_path')->store('cms/sections', 'public');
        }

        $validated['is_visible'] = $request->has('is_visible');

        $section->update($validated);

        return redirect()->route('admin.cms.pages.sections.index', $page)->with('success', 'Section updated successfully.');
    }

    public function destroy(CmsPage $page, PageSection $section)
    {
        if ($section->image_path) {
            Storage::disk('public')->delete($section->image_path);
        }
        $section->delete();
        return redirect()->route('admin.cms.pages.sections.index', $page)->with('success', 'Section removed.');
    }
}
