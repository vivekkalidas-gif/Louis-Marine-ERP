<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificateTemplate;
use App\Models\EquipmentCategory;
use Illuminate\Http\Request;

class CertificateTemplateController extends Controller
{
    /**
     * Display a listing of certificate templates
     */
    public function index()
    {
        $templates = CertificateTemplate::with(['equipmentCategory', 'sections.fields'])
            ->withCount('sections')
            ->latest()
            ->get();

        return view('admin.certificate-templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new template
     */
    public function create()
    {
        $categories = EquipmentCategory::where('is_active', true)->orderBy('name')->get();
        return view('admin.certificate-templates.create', compact('categories'));
    }

    /**
     * Store a newly created template
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'equipment_category_id' => 'required|exists:equipment_categories,id',
            'title' => 'required|string|max:255',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['header_config'] = [
            'show_logo' => true,
            'show_company_name' => true,
        ];
        $validated['footer_config'] = [
            'show_qr_code' => true,
            'show_signature' => true,
        ];

        $template = CertificateTemplate::create($validated);

        // Log creation
        $template->logChange('created');

        return redirect()
            ->route('admin.certificate-templates.edit', $template)
            ->with('success', 'Template created successfully. Now add sections and fields.');
    }

    /**
     * Display the specified template
     */
    public function show(CertificateTemplate $certificateTemplate)
    {
        $certificateTemplate->load(['equipmentCategory', 'sections.fields', 'creator']);
        return view('admin.certificate-templates.show', compact('certificateTemplate'));
    }

    /**
     * Show the form for editing the template
     */
    public function edit(CertificateTemplate $certificateTemplate)
    {
        $certificateTemplate->load(['sections.fields', 'equipmentCategory']);
        $categories = EquipmentCategory::where('is_active', true)->orderBy('name')->get();
        
        return view('admin.certificate-templates.edit', compact('certificateTemplate', 'categories'));
    }

    /**
     * Update the specified template
     */
    public function update(Request $request, CertificateTemplate $certificateTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'equipment_category_id' => 'required|exists:equipment_categories,id',
            'title' => 'required|string|max:255',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        $original = $certificateTemplate->getOriginal();
        $certificateTemplate->update($validated);

        // Log update
        $certificateTemplate->logChange('updated', [
            'before' => $original,
            'after' => $certificateTemplate->getAttributes()
        ]);

        return redirect()
            ->route('admin.certificate-templates.edit', $certificateTemplate)
            ->with('success', 'Template updated successfully.');
    }

    /**
     * Remove the specified template
     */
    public function destroy(CertificateTemplate $certificateTemplate)
    {
        // Check if template is in use
        if ($certificateTemplate->certificates()->count() > 0) {
            return back()->with('error', 'Cannot delete template that is in use by certificates.');
        }

        $certificateTemplate->logChange('deleted');
        $certificateTemplate->delete();

        return redirect()
            ->route('admin.certificate-templates.index')
            ->with('success', 'Template deleted successfully.');
    }

    /**
     * Toggle template active status
     */
    public function toggle(CertificateTemplate $certificateTemplate)
    {
        $certificateTemplate->is_active = !$certificateTemplate->is_active;
        $certificateTemplate->save();

        $changeType = $certificateTemplate->is_active ? 'activated' : 'deactivated';
        $certificateTemplate->logChange($changeType);

        return back()->with('success', 'Template ' . $changeType . ' successfully.');
    }

    /**
     * Set template as default for its category
     */
    public function setDefault(CertificateTemplate $certificateTemplate)
    {
        // Remove default flag from other templates in same category
        CertificateTemplate::where('equipment_category_id', $certificateTemplate->equipment_category_id)
            ->where('id', '!=', $certificateTemplate->id)
            ->update(['is_default' => false]);

        $certificateTemplate->is_default = true;
        $certificateTemplate->save();

        return back()->with('success', 'Template set as default for this category.');
    }

    /**
     * Preview template structure
     */
    public function preview(Request $request, CertificateTemplate $certificateTemplate)
    {
        $certificateTemplate->load(['sections.fields']);
        
        if ($request->has('ajax')) {
            return view('admin.certificate-templates.partials.fields-inputs', [
                'sections' => $certificateTemplate->sections,
                'values' => [] // Empty for fresh create
            ]);
        }
        
        return view('admin.certificate-templates.preview', compact('certificateTemplate'));
    }

    public function export()
    {
        $filename = 'certificate-templates-export-' . date('Y-m-d') . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];
        
        $columns = array('Name', 'Category', 'Title', 'Active', 'Default');
        
        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            $templates = CertificateTemplate::with('equipmentCategory')->get();
            foreach ($templates as $template) {
                fputcsv($file, array(
                    $template->name, 
                    $template->equipmentCategory->name ?? 'N/A', 
                    $template->title,
                    $template->is_active ? 'Yes' : 'No',
                    $template->is_default ? 'Yes' : 'No'
                ));
            }
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    public function downloadTemplate()
    {
        $filename = 'certificate-template-import-template.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];
        
        $columns = array('name', 'category_name', 'title');
        
        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            // Example row
            fputcsv($file, array('Standard Fire Extinguisher', 'Fire Extinguisher', 'Certificate of Inspection'));
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');
        
        try {
            \Illuminate\Support\Facades\DB::beginTransaction();
            
            $handle = fopen($file->getPathname(), 'r');
            $header = fgetcsv($handle); // Skip header
            
            $count = 0;
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 3) continue; 
                
                $name = $row[0] ?? null;
                $categoryName = $row[1] ?? null;
                $title = $row[2] ?? null;
                
                if (!$name || !$categoryName || !$title) continue;
                
                // Find Category by name
                $category = EquipmentCategory::where('name', $categoryName)->first();
                if (!$category) continue; // Skip if category not found
                
                CertificateTemplate::updateOrCreate(
                    ['name' => $name, 'equipment_category_id' => $category->id],
                    [
                        'title' => $title,
                        'is_active' => true,
                        'created_by' => auth()->id(),
                        'header_config' => ['show_logo' => true, 'show_company_name' => true],
                        'footer_config' => ['show_qr_code' => true, 'show_signature' => true]
                    ]
                );
                $count++;
            }
            
            fclose($handle);
            \Illuminate\Support\Facades\DB::commit();
            
            return redirect()->route('admin.certificate-templates.index')->with('success', "Successfully imported $count templates.");
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Error importing file: ' . $e->getMessage());
        }
    }
}
