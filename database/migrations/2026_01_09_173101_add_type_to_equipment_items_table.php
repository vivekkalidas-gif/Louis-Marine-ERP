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
        Schema::table('equipment_items', function (Blueprint $table) {
            $table->string('record_type')->default('asset')->after('is_active')->index();
        });

        // Update seeded items to be templates
        $templateCodes = [
            'FE-CO2-2KG', 'FE-CO2-5KG', 'FE-DCP-9KG',
            'SCBA-30MIN', 'SCBA-45MIN',
            'LR-6P-TO', 'LR-10P-DL',
            'LJ-ADULT-SOLAS',
            'IS-L',
            'EEBD-10MIN', 'EEBD-15MIN',
            'CO2-MAIN-BANK', 'CO2-PILOT',
            'GD-PORTABLE', 'GD-FIXED'
        ];

        DB::table('equipment_items')
            ->whereIn('code', $templateCodes)
            ->update(['record_type' => 'template']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipment_items', function (Blueprint $table) {
            $table->dropColumn('record_type');
        });
    }
};
