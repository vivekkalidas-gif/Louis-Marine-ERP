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
        Schema::table('contact_submissions', function (Blueprint $table) {
            if (!Schema::hasColumn('contact_submissions', 'company')) {
                $table->string('company')->nullable()->after('email');
            }
            if (!Schema::hasColumn('contact_submissions', 'service_type')) {
                $table->string('service_type')->nullable()->after('subject');
            }
            if (!Schema::hasColumn('contact_submissions', 'status')) {
                $table->string('status')->default('new')->after('message');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_submissions', function (Blueprint $table) {
            $table->dropColumn(['company', 'service_type', 'status']);
        });
    }
};
