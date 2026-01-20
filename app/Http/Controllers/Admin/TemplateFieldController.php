<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificateTemplateSection;
use App\Models\CertificateTemplateField;
use Illuminate\Http\Request;

class TemplateFieldController extends Controller
{
    /**
     * Store a new field
     */
    public function store(Request $request, CertificateTemplateSection $section)
    {
        $validated = $request->validate([
            'field_name' => 'required|string|max:255',
            'field_label' => 'required|string|max:255',
            'field_type' => 'required|in:text,textarea,number,date,dropdown,checkbox,radio',
            'field_options' => 'nullable|array',
            'is_required' => 'boolean',
            'placeholder' => 'nullable|string|max:255',
            'help_text' => 'nullable|string',
            'default_value' => 'nullable|string',
            'width' => 'required|in:full,half,third',
        ]);

        // Get next display order
        $maxOrder = $section->fields()->max('display_order') ?? 0;
        $validated['display_order'] = $maxOrder + 1;
        $validated['section_id'] = $section->id;

        // Handle field options for dropdown/radio
        if (in_array($validated['field_type'], ['dropdown', 'radio']) && !empty($validated['field_options'])) {
            $validated['field_options'] = ['options' => $validated['field_options']];
        }

        $field = CertificateTemplateField::create($validated);

        return redirect()
            ->route('admin.certificate-templates.edit', $section->template)
            ->with('success', 'Field added successfully.');
    }

    /**
     * Update a field
     */
    public function update(Request $request, CertificateTemplateField $field)
    {
        $validated = $request->validate([
            'field_name' => 'required|string|max:255',
            'field_label' => 'required|string|max:255',
            'field_type' => 'required|in:text,textarea,number,date,dropdown,checkbox,radio',
            'field_options' => 'nullable|array',
            'is_required' => 'boolean',
            'placeholder' => 'nullable|string|max:255',
            'help_text' => 'nullable|string',
            'default_value' => 'nullable|string',
            'width' => 'required|in:full,half,third',
        ]);

        // Handle field options
        if (in_array($validated['field_type'], ['dropdown', 'radio']) && !empty($validated['field_options'])) {
            $validated['field_options'] = ['options' => $validated['field_options']];
        }



        $validated['is_required'] = $request->boolean('is_required');

        $field->update($validated);

        return redirect()
            ->route('admin.certificate-templates.edit', $field->section->template)
            ->with('success', 'Field updated successfully.');
    }

    /**
     * Delete a field
     */
    public function destroy(CertificateTemplateField $field)
    {
        $template = $field->section->template;
        
        // Check if field has values
        if ($field->values()->count() > 0) {
            return back()->with('error', 'Cannot delete field that has data in certificates.');
        }

        $field->delete();

        return redirect()
            ->route('admin.certificate-templates.edit', $template)
            ->with('success', 'Field deleted successfully.');
    }

    /**
     * Reorder fields
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'fields' => 'required|array',
            'fields.*.id' => 'required|exists:certificate_template_fields,id',
            'fields.*.order' => 'required|integer',
        ]);

        foreach ($validated['fields'] as $fieldData) {
            CertificateTemplateField::where('id', $fieldData['id'])
                ->update(['display_order' => $fieldData['order']]);
        }

        return response()->json(['success' => true]);
    }
}
