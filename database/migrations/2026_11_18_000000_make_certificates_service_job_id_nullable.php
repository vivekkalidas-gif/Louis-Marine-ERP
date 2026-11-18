<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Certificates take a manually typed job number, so a linked service job is optional.
 * The create migration already declares service_job_id nullable; this only repairs
 * databases where the column ended up NOT NULL. No-op everywhere else.
 */
return new class extends Migration
{
    public function up(): void
    {
        $column = collect(Schema::getColumns('certificates'))->firstWhere('name', 'service_job_id');

        if ($column && ! $column['nullable']) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->uuid('service_job_id')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        // Intentionally left nullable: certificates without a linked job may exist.
    }
};
