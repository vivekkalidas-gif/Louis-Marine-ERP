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
        if (!Schema::hasTable('incoterms')) {
            Schema::create('incoterms', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique(); // e.g., EXW
                $table->string('name')->nullable(); // e.g., Ex Works
            });

            // Seed Default Incoterms
            DB::table('incoterms')->insert([
                ['code' => 'EXW', 'name' => 'Ex Works'],
                ['code' => 'FCA', 'name' => 'Free Carrier'],
                ['code' => 'CPT', 'name' => 'Carriage Paid To'],
                ['code' => 'CIP', 'name' => 'Carriage and Insurance Paid To'],
                ['code' => 'DAP', 'name' => 'Delivered at Place'],
                ['code' => 'DPU', 'name' => 'Delivered at Place Unloaded'],
                ['code' => 'DDP', 'name' => 'Delivered Duty Paid'],
                ['code' => 'FAS', 'name' => 'Free Alongside Ship'],
                ['code' => 'FOB', 'name' => 'Free on Board'],
                ['code' => 'CFR', 'name' => 'Cost and Freight'],
                ['code' => 'CIF', 'name' => 'Cost, Insurance and Freight'],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incoterms');
    }
};
