<?php

namespace Database\Seeders;

use App\Models\CertificateTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AddInspectionReportFieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = CertificateTemplate::all();

        foreach ($templates as $template) {
            // Check if "Inspection Report" section already exists
            $section = $template->sections()->where('name', 'Inspection Report')->first();

            if (!$section) {
                // Determine max display order
                $maxOrder = $template->sections()->max('display_order') ?? 0;

                // Create Section
                $section = $template->sections()->create([
                    'name' => 'Inspection Report',
                    'description' => 'Findings and remarks',
                    'layout_type' => 'single_column',
                    'is_repeatable' => false,
                    'display_order' => $maxOrder + 1,
                ]);

                $this->command->info("Added 'Inspection Report' section to template: {$template->name}");
            }

            // Check if field exists
            $field = $section->fields()->where('field_label', 'Inspection Report')->first();

            if (!$field) {
                $section->fields()->create([
                    'field_name' => 'inspection_report',
                    'field_label' => 'Inspection Report',
                    'field_type' => 'textarea',
                    'width' => 'full',
                    'is_required' => false,
                    'placeholder' => 'Enter inspection findings...',
                    'display_order' => 1,
                ]);
                $this->command->info("Added 'Inspection Report' field to template: {$template->name}");
            }
        }
    }
}
