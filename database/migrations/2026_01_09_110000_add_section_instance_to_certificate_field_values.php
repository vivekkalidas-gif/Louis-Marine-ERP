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
        Schema::table('certificate_field_values', function (Blueprint $table) {
            $table->integer('section_instance')->default(1)->after('certificate_template_field_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificate_field_values', function (Blueprint $table) {
            $table->dropColumn('section_instance');
        });
    }
};
