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
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->decimal('transport_charges', 15, 2)->default(0)->after('total_amount');
            $table->decimal('customs_charges', 15, 2)->default(0)->after('transport_charges');
            $table->decimal('other_charges', 15, 2)->default(0)->after('customs_charges');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['transport_charges', 'customs_charges', 'other_charges']);
        });
    }
};
