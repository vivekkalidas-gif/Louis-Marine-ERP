<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'users',
            'certificates',
            'certificate_templates',
            'certificate_types',
            'cms_certifications',
            'cms_partners',
            'cms_services',
            'cms_cta_sections',
            'customers',
            'customer_contacts',
            'customer_documents',
            'customer_notes',
            'dispatch_notes',
            'employee_details',
            'employee_documents',
            'employee_ledger_entries',
            'equipment_categories',
            'equipment_items',
            'expenses',
            'cms_features',
            'finance_categories',
            'cms_hero_slides',
            'incomes',
            'inventory_categories',
            'invoices',
            'overtime_records',
            'payments',
            'payrolls',
            'payroll_deductions',
            'petty_cash_entries',
            'products',
            'purchase_orders',
            'quotations',
            'quote_requests',
            'rfqs',
            'roles',
            'service_jobs',
            'service_job_documents',
            'service_reports',
            'cms_stats',
            'suppliers',
            'cms_testimonials',
            'vendor_bills',
            'vendor_payments',
            'vessels',
            'website_categories',
            'website_products',
            'workshop_tools',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->softDeletes();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op to avoid dropping columns unintentionally
    }
};
