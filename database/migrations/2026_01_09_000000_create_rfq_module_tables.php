<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Cleanup potential partial migration artifacts
        // Schema::dropIfExists('supplier_quotations');
        // Schema::dropIfExists('rfq_suppliers');
        // Schema::dropIfExists('rfq_items');
        // Schema::dropIfExists('rfqs');
        // Schema::dropIfExists('currencies');

        // 1. Currencies Table
        if (!Schema::hasTable('currencies')) {
            Schema::create('currencies', function (Blueprint $table) {
                $table->id();
                $table->string('code', 3)->unique(); // USD, EUR, QAR
                $table->string('name');
                $table->string('symbol', 10);
                $table->decimal('exchange_rate', 15, 6)->default(1.000000); // Rate relative to QAR (Base)
                $table->boolean('is_base')->default(false);
                $table->timestamps();
            });

            // Seed Default Currencies
            DB::table('currencies')->insert([
                ['code' => 'QAR', 'name' => 'Qatari Riyal', 'symbol' => 'QR', 'exchange_rate' => 1.000000, 'is_base' => true, 'created_at' => now(), 'updated_at' => now()],
                ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'exchange_rate' => 3.650000, 'is_base' => false, 'created_at' => now(), 'updated_at' => now()],
                ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'exchange_rate' => 4.000000, 'is_base' => false, 'created_at' => now(), 'updated_at' => now()],
                ['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£', 'exchange_rate' => 4.600000, 'is_base' => false, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // 2. RFQs Table
        if (!Schema::hasTable('rfqs')) {
            Schema::create('rfqs', function (Blueprint $table) {
                $table->id();
                $table->string('reference_number')->unique(); // RFQ/2025/001
                $table->enum('type', ['local', 'overseas'])->default('local');
                $table->enum('status', ['draft', 'sent', 'received', 'approved', 'po_created', 'cancelled'])->default('draft');
                
                // Overseas specific
                $table->foreignId('currency_id')->nullable()->constrained('currencies');
                $table->string('incoterm')->nullable(); // FOB, CIF, EXW
                $table->string('delivery_location')->nullable(); 

                $table->date('deadline_date')->nullable();
                
                // Meta
                $table->foreignUuid('created_by')->constrained('users');
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 3. RFQ Items (What we are asking for)
        if (!Schema::hasTable('rfq_items')) {
            Schema::create('rfq_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('rfq_id')->constrained('rfqs')->cascadeOnDelete();
                
                // Link to Material Request
                $table->unsignedBigInteger('material_request_id')->nullable(); 
                // Note: Not strictly constrained to avoid issues if table name differs, implies 'material_requests.id'
                
                $table->foreignId('product_id')->nullable()->constrained('inventory_items'); 
                $table->text('description');
                $table->decimal('quantity', 15, 2);
                $table->string('unit')->nullable();
                
                $table->timestamps();
            });
        }

        // 4. RFQ Suppliers (Who we sent it to)
        if (!Schema::hasTable('rfq_suppliers')) {
            Schema::create('rfq_suppliers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('rfq_id')->constrained('rfqs')->cascadeOnDelete();
                
                // FIXED: Use foreignUuid because Supplier model uses HasUuids
                $table->foreignUuid('supplier_id')->constrained('suppliers');
                
                $table->timestamp('sent_at')->nullable();
                $table->timestamp('responded_at')->nullable();
                $table->string('supplier_ref')->nullable(); // Supplier's Quote #
                
                $table->timestamps();
                
                $table->unique(['rfq_id', 'supplier_id']);
            });
        }

        // 5. Supplier Quotations (The response details)
        if (!Schema::hasTable('supplier_quotations')) {
            Schema::create('supplier_quotations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('rfq_id')->constrained('rfqs')->cascadeOnDelete();
                
                // FIXED: Use foreignUuid because Supplier model uses HasUuids
                $table->foreignUuid('supplier_id')->constrained('suppliers');
                
                // Costs
                $table->decimal('subtotal_amount', 15, 2); // In Supplier Currency
                $table->decimal('tax_amount', 15, 2)->default(0); 
                $table->decimal('shipping_cost', 15, 2)->default(0);
                $table->decimal('insurance_cost', 15, 2)->default(0);
                $table->decimal('customs_duty', 15, 2)->default(0);
                $table->decimal('other_costs', 15, 2)->default(0);
                
                $table->decimal('total_amount_foreign', 15, 2); // Total in Supplier Currency
                $table->decimal('exchange_rate', 15, 6)->default(1); 
                $table->decimal('total_amount_local', 15, 2); // Calculated Landed Cost in Base Currency
                
                // Details
                $table->string('lead_time')->nullable(); // e.g. "2 Weeks"
                $table->string('warranty')->nullable();
                $table->string('payment_terms')->nullable();
                $table->string('quotation_file')->nullable(); // Path to PDF
                
                $table->boolean('is_selected')->default(false);
                $table->text('selection_note')->nullable();

                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_quotations');
        Schema::dropIfExists('rfq_suppliers');
        Schema::dropIfExists('rfq_items');
        Schema::dropIfExists('rfqs');
        Schema::dropIfExists('currencies');
    }
};
