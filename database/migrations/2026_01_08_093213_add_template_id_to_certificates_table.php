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
        if (!Schema::hasColumn('certificates', 'certificate_template_id')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->foreignUuid('certificate_template_id')->nullable()->after('certificate_type_id')->constrained('certificate_templates')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('certificates', 'certificate_template_id')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->dropForeign(['certificate_template_id']);
                $table->dropColumn('certificate_template_id');
            });
        }
    }
};
