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
            if (!Schema::hasColumn('suppliers', 'tax_id')) {
                $table->string('tax_id')->nullable()->after('address');
            }
            if (!Schema::hasColumn('suppliers', 'payment_terms')) {
                $table->string('payment_terms')->nullable()->after('tax_id');
            }
            if (!Schema::hasColumn('suppliers', 'created_by')) {
                $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete()->after('payment_terms');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
             $table->dropForeign(['created_by']);
             $table->dropColumn(['tax_id', 'payment_terms', 'created_by']);
        });
    }
};
