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
        Schema::table('payments', function (Blueprint $table) {
            // Drop constraint to make nullable, or just modify
            // In SQLite/MySQL sometimes constraints are tricky. safest is modify.
            // However, Doctrine DBAL is needed for 'change'.
            // Assuming MySQL.
            
            // 1. Add customer_id first (nullable initially to avoid errors with existing data)
            $table->foreignUuid('customer_id')->nullable()->after('payment_number')->constrained()->onDelete('cascade');
            
            // 2. Add status
            $table->string('status')->default('allocated')->after('amount'); // allocated, unallocated, partial
        });
        
        // Make invoice_id nullable
        Schema::table('payments', function (Blueprint $table) {
            $table->uuid('invoice_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
            $table->dropColumn('status');
            $table->uuid('invoice_id')->nullable(false)->change();
        });
    }
};
