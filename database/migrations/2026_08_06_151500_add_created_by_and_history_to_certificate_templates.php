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
        Schema::table('certificate_templates', function (Blueprint $table) {
            if (!Schema::hasColumn('certificate_templates', 'created_by')) {
                $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            }
        });

        if (!Schema::hasTable('certificate_template_histories')) {
            Schema::create('certificate_template_histories', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('template_id')->constrained('certificate_templates')->cascadeOnDelete();
                $table->foreignUuid('changed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->string('change_type');
                $table->json('changes')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate_template_histories');

        Schema::table('certificate_templates', function (Blueprint $table) {
            if (Schema::hasColumn('certificate_templates', 'created_by')) {
                $table->dropColumn('created_by');
            }
        });
    }
};
