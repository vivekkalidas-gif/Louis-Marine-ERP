<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use App\Models\WebsiteProduct;
use App\Models\CmsService;
use App\Models\ContactSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CmsController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_products' => WebsiteProduct::count(),
            'active_products' => WebsiteProduct::where('is_active', true)->count(),
            'services' => CmsService::count(),
            'enquiries' => ContactSubmission::where('status', 'new')->count(),
        ];

        return view('admin.cms.dashboard', compact('stats'));
    }

    public function index()
    {
        $pages = CmsPage::all();
        return view('admin.cms.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.cms.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required',
            'content' => 'required',
        ]);

        $validated['slug'] = Str::slug($request->title);
        $validated['is_published'] = $request->has('is_published');

        CmsPage::create($validated);

        return redirect()->route('admin.cms-pages.index')->with('success', 'Page created.');
    }

    public function edit(CmsPage $cmsPage)
    {
        return view('admin.cms.edit', compact('cmsPage'));
    }

    public function update(Request $request, CmsPage $cmsPage)
    {
        $validated = $request->validate([
            'title' => 'required',
            'content' => 'required',
        ]);

        $validated['is_published'] = $request->has('is_published');
        // Slug update optional or handled carefully

        $cmsPage->update($validated);

        return redirect()->route('admin.cms-pages.index')->with('success', 'Page updated.');
    }

    public function destroy(CmsPage $cmsPage)
    {
        $cmsPage->delete();
        return redirect()->route('admin.cms-pages.index')->with('success', 'Page deleted.');
    }
}
