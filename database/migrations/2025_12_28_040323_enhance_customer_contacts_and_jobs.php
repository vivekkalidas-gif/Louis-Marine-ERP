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
        // 1. Update Customer Contacts
        Schema::table('customer_contacts', function (Blueprint $table) {
            if (!Schema::hasColumn('customer_contacts', 'designation')) {
                $table->string('designation')->nullable();
            }
            if (!Schema::hasColumn('customer_contacts', 'contact_type')) {
                $table->string('contact_type')->default('General');
            }
            if (!Schema::hasColumn('customer_contacts', 'can_request_job')) {
                $table->boolean('can_request_job')->default(false);
            }
            if (!Schema::hasColumn('customer_contacts', 'can_receive_invoice')) {
                $table->boolean('can_receive_invoice')->default(false);
            }
            if (!Schema::hasColumn('customer_contacts', 'status')) {
                $table->string('status')->default('Active');
            }
            if (!Schema::hasColumn('customer_contacts', 'is_primary')) {
                 $table->boolean('is_primary')->default(false)->change(); // Ensure default
            } else {
                 // Creating if not exists, skipping if exists.
                 // Cannot easily "add if not exists" for is_primary as it was in original create.
            }
        });

        // 2. Update Service Jobs
        Schema::table('service_jobs', function (Blueprint $table) {
            if (!Schema::hasColumn('service_jobs', 'requested_by_contact_id')) {
                $table->foreignUuid('requested_by_contact_id')->nullable()->constrained('customer_contacts')->nullOnDelete();
            }
        });

        // 3. Create Invoice Contacts (Pivot)
        if (!Schema::hasTable('invoice_contacts')) {
            Schema::create('invoice_contacts', function (Blueprint $table) {
                $table->id();
                $table->foreignUuid('invoice_id')->constrained()->cascadeOnDelete();
                $table->foreignUuid('customer_contact_id')->constrained()->cascadeOnDelete();
                $table->string('type')->default('to');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_contacts');
        
        Schema::table('service_jobs', function (Blueprint $table) {
            $table->dropForeign(['requested_by_contact_id']);
            $table->dropColumn('requested_by_contact_id');
        });

        Schema::table('customer_contacts', function (Blueprint $table) {
            $table->dropColumn([
                'designation', 
                'contact_type', 
                'can_request_job', 
                'can_receive_invoice', 
                'is_primary',
                'status'
            ]);
        });
    }
};
