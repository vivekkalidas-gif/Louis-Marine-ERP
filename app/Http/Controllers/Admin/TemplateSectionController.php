<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificateTemplate;
use App\Models\CertificateTemplateSection;
use Illuminate\Http\Request;

class TemplateSectionController extends Controller
{
    /**
     * Store a new section
     */
    public function store(Request $request, CertificateTemplate $certificateTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_repeatable' => 'boolean',
            'layout_type' => 'required|in:single_column,two_column,table',
        ]);

        // Get next display order
        $maxOrder = $certificateTemplate->sections()->max('display_order') ?? 0;
        $validated['display_order'] = $maxOrder + 1;
        $validated['certificate_template_id'] = $certificateTemplate->id;

        $section = CertificateTemplateSection::create($validated);

        return redirect()
            ->route('admin.certificate-templates.edit', $certificateTemplate)
            ->with('success', 'Section added successfully.');
    }

    /**
     * Update a section
     */
    public function update(Request $request, CertificateTemplateSection $section)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_repeatable' => 'boolean',
            'layout_type' => 'required|in:single_column,two_column,table',
        ]);

        $validated['is_repeatable'] = $request->boolean('is_repeatable');

        $section->update($validated);

        return redirect()
            ->route('admin.certificate-templates.edit', $section->template)
            ->with('success', 'Section updated successfully.');
    }

    /**
     * Delete a section
     */
    public function destroy(CertificateTemplateSection $section)
    {
        $template = $section->template;
        $section->delete();

        return redirect()
            ->route('admin.certificate-templates.edit', $template)
            ->with('success', 'Section deleted successfully.');
    }

    /**
     * Reorder sections
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'sections' => 'required|array',
            'sections.*.id' => 'required|exists:certificate_template_sections,id',
            'sections.*.order' => 'required|integer',
        ]);

        foreach ($validated['sections'] as $sectionData) {
            CertificateTemplateSection::where('id', $sectionData['id'])
                ->update(['display_order' => $sectionData['order']]);
        }

        return response()->json(['success' => true]);
    }
}
