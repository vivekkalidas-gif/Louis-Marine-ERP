<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HeroSlideController extends Controller
{
    public function index()
    {
        $slides = HeroSlide::ordered()->get();
        return view('admin.cms.landing-page.hero-slides.index', compact('slides'));
    }

    public function create()
    {
        return view('admin.cms.landing-page.hero-slides.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'background_image' => 'nullable|image|max:2048',
            'background_video_url' => 'nullable|url',
            'cta_text_1' => 'nullable|string|max:100',
            'cta_link_1' => 'nullable|string|max:255',
            'cta_text_2' => 'nullable|string|max:100',
            'cta_link_2' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        if ($request->hasFile('background_image')) {
            $validated['background_image'] = $request->file('background_image')->store('hero-slides', 'public');
        }

        HeroSlide::create($validated);

        return redirect()->route('admin.cms.landing-page.hero-slides.index')
            ->with('success', 'Hero slide created successfully.');
    }

    public function edit(HeroSlide $heroSlide)
    {
        return view('admin.cms.landing-page.hero-slides.edit', compact('heroSlide'));
    }

    public function update(Request $request, HeroSlide $heroSlide)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'background_image' => 'nullable|image|max:2048',
            'background_video_url' => 'nullable|url',
            'cta_text_1' => 'nullable|string|max:100',
            'cta_link_1' => 'nullable|string|max:255',
            'cta_text_2' => 'nullable|string|max:100',
            'cta_link_2' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        if ($request->hasFile('background_image')) {
            if ($heroSlide->background_image) {
                Storage::disk('public')->delete($heroSlide->background_image);
            }
            $validated['background_image'] = $request->file('background_image')->store('hero-slides', 'public');
        }

        $heroSlide->update($validated);

        return redirect()->route('admin.cms.landing-page.hero-slides.index')
            ->with('success', 'Hero slide updated successfully.');
    }

    public function destroy(HeroSlide $heroSlide)
    {
        if ($heroSlide->background_image) {
            Storage::disk('public')->delete($heroSlide->background_image);
        }
        
        $heroSlide->delete();

        return redirect()->route('admin.cms.landing-page.hero-slides.index')
            ->with('success', 'Hero slide deleted successfully.');
    }

    public function reorder(Request $request)
    {
        $order = $request->input('order', []);
        
        foreach ($order as $index => $id) {
            HeroSlide::where('id', $id)->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true]);
    }
}
