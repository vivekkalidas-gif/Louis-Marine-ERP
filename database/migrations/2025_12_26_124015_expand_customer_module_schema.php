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
        // 1. Update Customers
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'type')) {
                $table->string('type')->default('Operator')->after('company_name');
            }
            if (!Schema::hasColumn('customers', 'status')) {
                $table->string('status')->default('Active')->after('type');
            }
            if (!Schema::hasColumn('customers', 'notes')) {
                $table->text('notes')->nullable();
            }
        });

        // 2. Update Vessels (Columns first)
        Schema::table('vessels', function (Blueprint $table) {
            if (!Schema::hasColumn('vessels', 'customer_id')) {
                $table->uuid('customer_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('vessels', 'year_built')) {
                $table->string('year_built')->nullable();
            }
            if (!Schema::hasColumn('vessels', 'port_of_registry')) {
                $table->string('port_of_registry')->nullable();
            }
            if (!Schema::hasColumn('vessels', 'official_number')) {
                $table->string('official_number')->nullable();
            }
            if (!Schema::hasColumn('vessels', 'call_sign')) {
                $table->string('call_sign')->nullable();
            }
        });

        // 3. Update Vessels (Foreign Key)
        // 3. Update Vessels (Foreign Key)
        // Schema::table('vessels', function (Blueprint $table) {
            // Check if index exists before adding? Laravel usually handles this, but let's be explicit and safe.
            // We'll just add the index/fk. If it fails, we know why.
             // $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
        // });

        // 4. Create Customer Contacts
        if (!Schema::hasTable('customer_contacts')) {
            Schema::create('customer_contacts', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('customer_id');
                $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
                $table->string('name');
                $table->string('role')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->boolean('is_primary')->default(false);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 5. Create Customer Documents
        if (!Schema::hasTable('customer_documents')) {
            Schema::create('customer_documents', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('customer_id');
                $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
                $table->uuid('vessel_id')->nullable();
                $table->foreign('vessel_id')->references('id')->on('vessels')->nullOnDelete();
                $table->string('title');
                $table->string('file_path');
                $table->date('expiry_date')->nullable();
                $table->text('description')->nullable();
                $table->foreignUuid('uploaded_by')->nullable()->constrained('users');
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 6. Create Customer Notes
        if (!Schema::hasTable('customer_notes')) {
            Schema::create('customer_notes', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('customer_id');
                $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
                $table->text('content');
                $table->foreignUuid('user_id')->constrained('users');
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
