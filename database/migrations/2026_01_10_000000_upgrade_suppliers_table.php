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
        Schema::table('suppliers', function (Blueprint $table) {
            // Check if columns exist before adding them (defensive coding)
            if (!Schema::hasColumn('suppliers', 'type')) {
                $table->string('type')->default('local')->after('name'); // local, overseas
            }
            if (!Schema::hasColumn('suppliers', 'category')) {
                $table->string('category')->nullable()->after('type'); // manufacturer, trader, service
            }
            if (!Schema::hasColumn('suppliers', 'status')) {
                $table->string('status')->default('active')->after('category'); // active, inactive, blacklisted
            }
            if (!Schema::hasColumn('suppliers', 'website')) {
                $table->string('website')->nullable()->after('email');
            }
            if (!Schema::hasColumn('suppliers', 'rating')) {
                $table->tinyInteger('rating')->default(0)->after('status'); // 1-5
            }
            
            // Foreign Keys for Overseas
            if (!Schema::hasColumn('suppliers', 'currency_id')) {
                $table->foreignId('currency_id')->nullable()->constrained('currencies')->nullOnDelete()->after('rating');
            }
            if (!Schema::hasColumn('suppliers', 'incoterm')) {
                $table->string('incoterm')->nullable()->after('currency_id');
            }
        });

        // Create Supplier Documents Table if not exists
        if (!Schema::hasTable('supplier_documents')) {
            Schema::create('supplier_documents', function (Blueprint $table) {
                $table->id();
                $table->foreignUuid('supplier_id')->constrained('suppliers')->cascadeOnDelete();
                $table->string('title'); // e.g., Trade License
                $table->string('file_path');
                $table->date('expiry_date')->nullable();
                $table->boolean('is_compliant')->default(true);
                $table->text('remarks')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_documents');

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropForeign(['currency_id']);
            $table->dropColumn(['type', 'category', 'status', 'website', 'rating', 'currency_id', 'incoterm']);
        });
    }
};
