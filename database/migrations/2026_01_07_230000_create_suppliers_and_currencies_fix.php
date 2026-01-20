<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Currencies
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

        // 2. Suppliers
        if (!Schema::hasTable('suppliers')) {
             Schema::create('suppliers', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->string('type')->default('local');
                $table->string('status')->default('active');
                $table->string('category')->nullable();
                $table->string('contact_person')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->string('website')->nullable();
                $table->text('address')->nullable();
                $table->string('tax_id')->nullable();
                $table->foreignId('currency_id')->nullable(); // Loosely linked to avoid strict constraint issues during fix
                $table->string('incoterm')->nullable();
                $table->string('payment_terms')->nullable();
                $table->integer('rating')->default(0);
                $table->foreignUuid('created_by')->nullable();
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
        // Don't drop, safety first
    }
};
