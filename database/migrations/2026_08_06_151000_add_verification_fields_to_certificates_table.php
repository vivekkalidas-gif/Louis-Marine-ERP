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
        Schema::table('certificates', function (Blueprint $table) {
            if (!Schema::hasColumn('certificates', 'assigned_verifier_id')) {
                $table->foreignUuid('assigned_verifier_id')->nullable()->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('certificates', 'verified_by')) {
                $table->foreignUuid('verified_by')->nullable()->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('certificates', 'verified_at')) {
                $table->timestamp('verified_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $columnsToDrop = array_filter([
                'assigned_verifier_id',
                'verified_by',
                'verified_at',
            ], fn($col) => Schema::hasColumn('certificates', $col));

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
