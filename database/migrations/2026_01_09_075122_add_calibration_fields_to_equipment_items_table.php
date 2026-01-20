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
        Schema::table('equipment_items', function (Blueprint $table) {
            $table->boolean('calibration_required')->default(false)->after('is_active');
            $table->date('calibration_expiry_date')->nullable()->after('calibration_required');
            $table->string('certificate_path')->nullable()->after('calibration_expiry_date');
            $table->foreignUuid('assigned_engineer_id')->nullable()->after('certificate_path')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipment_items', function (Blueprint $table) {
            $table->dropForeign(['assigned_engineer_id']);
            $table->dropColumn(['calibration_required', 'calibration_expiry_date', 'certificate_path', 'assigned_engineer_id']);
        });
    }
};
