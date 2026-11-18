<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Models\CmsService;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function index()
    {
        // Auto-sync from ERP ServiceTypes if CMS Services catalog is empty
        if (CmsService::count() === 0) {
            $this->autoSyncFromServiceTypes();
        }

        $services = CmsService::orderBy('order')->paginate(10);
        return view('admin.cms.services.index', compact('services'));
    }

    public function syncFromErp()
    {
        $count = $this->autoSyncFromServiceTypes();
        return redirect()->route('admin.cms.services.index')
            ->with('success', $count > 0 ? "Successfully synced {$count} service(s) from ERP Master Data." : "CMS Services catalog is already up to date with ERP Master Data.");
    }

    private function autoSyncFromServiceTypes()
    {
        $serviceTypes = ServiceType::all();
        $synced = 0;
        foreach ($serviceTypes as $index => $st) {
            $slug = Str::slug($st->name);
            $existing = CmsService::where('slug', $slug)->orWhere('title', $st->name)->first();
            if (!$existing) {
                $maxOrder = CmsService::max('order') ?? 0;
                CmsService::create([
                    'title' => $st->name,
                    'slug' => $slug,
                    'description' => $st->description ?? ($st->name . ' marine engineering and inspection service.'),
                    'is_active' => true,
                    'order' => $maxOrder + 1,
                ]);
                $synced++;
            }
        }
        return $synced;
    }

    public function create()
    {
        return view('admin.cms.services.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required',
            'description' => 'required',
            'content' => 'nullable',
            'icon_class' => 'nullable',
            'image_path' => 'nullable|image|max:2048',
            'order' => 'integer',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image_path')) {
            $validated['image_path'] = $request->file('image_path')->store('cms/services', 'public');
        }

        $validated['slug'] = Str::slug($request->title);
        $validated['is_active'] = $request->has('is_active');

        CmsService::create($validated);
        return redirect()->route('admin.cms.services.index')->with('success', 'Service created successfully.');
    }

    public function edit(CmsService $service)
    {
        return view('admin.cms.services.edit', compact('service'));
    }

    public function update(Request $request, CmsService $service)
    {
        $validated = $request->validate([
            'title' => 'required',
            'description' => 'required',
            'content' => 'nullable',
            'icon_class' => 'nullable',
            'image_path' => 'nullable|image|max:2048',
            'order' => 'integer',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image_path')) {
            if ($service->image_path) {
                Storage::disk('public')->delete($service->image_path);
            }
            $validated['image_path'] = $request->file('image_path')->store('cms/services', 'public');
        }

        $validated['slug'] = Str::slug($request->title);
        $validated['is_active'] = $request->has('is_active');

        $service->update($validated);
        return redirect()->route('admin.cms.services.index')->with('success', 'Service updated successfully.');
    }

    public function destroy(CmsService $service)
    {
        if ($service->image_path) {
            Storage::disk('public')->delete($service->image_path);
        }
        $service->delete();
        return redirect()->route('admin.cms.services.index')->with('success', 'Service deleted.');
    }
}
