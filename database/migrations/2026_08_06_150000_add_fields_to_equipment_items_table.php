<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('equipment_items', function (Blueprint $table) {
            if (!Schema::hasColumn('equipment_items', 'record_type')) {
                $table->string('record_type')->default('asset')->after('category_id');
            }
            if (!Schema::hasColumn('equipment_items', 'assigned_engineer_id')) {
                $table->foreignUuid('assigned_engineer_id')->nullable()->after('category_id');
            }
            if (!Schema::hasColumn('equipment_items', 'calibration_required')) {
                $table->boolean('calibration_required')->default(false)->after('is_active');
            }
            if (!Schema::hasColumn('equipment_items', 'calibration_expiry_date')) {
                $table->date('calibration_expiry_date')->nullable()->after('calibration_required');
            }
            if (!Schema::hasColumn('equipment_items', 'certificate_path')) {
                $table->string('certificate_path')->nullable()->after('calibration_expiry_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('equipment_items', function (Blueprint $table) {
            $columnsToDrop = array_filter([
                'record_type',
                'assigned_engineer_id',
                'calibration_required',
                'calibration_expiry_date',
                'certificate_path'
            ], fn($col) => Schema::hasColumn('equipment_items', $col));

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
