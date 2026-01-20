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
        $sections = $page->sections;
        return view('admin.cms.pages.sections.index', compact('page', 'sections'));
    }

    public function store(Request $request, CmsPage $page)
    {
        $validated = $request->validate([
            'section_key' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'order' => 'integer',
            'is_visible' => 'boolean'
        ]);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('cms/sections', 'public');
        }

        $page->sections()->create([
            'section_key' => $validated['section_key'],
            'title' => $validated['title'],
            'content' => $validated['content'],
            'image_path' => $validated['image_path'] ?? null,
            'order' => $validated['order'] ?? 0,
            'is_visible' => $request->has('is_visible'),
        ]);

        return back()->with('success', 'Section added successfully.');
    }

    public function update(Request $request, PageSection $section)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'order' => 'integer',
            'is_visible' => 'boolean'
        ]);

        if ($request->hasFile('image')) {
            if ($section->image_path) {
                Storage::disk('public')->delete($section->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('cms/sections', 'public');
        }

        $section->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'image_path' => $validated['image_path'] ?? $section->image_path,
            'order' => $validated['order'] ?? $section->order,
            'is_visible' => $request->has('is_visible'),
        ]);

        return back()->with('success', 'Section updated successfully.');
    }

    public function destroy(PageSection $section)
    {
        if ($section->image_path) {
            Storage::disk('public')->delete($section->image_path);
        }
        $section->delete();
        return back()->with('success', 'Section deleted successfully.');
    }
}
