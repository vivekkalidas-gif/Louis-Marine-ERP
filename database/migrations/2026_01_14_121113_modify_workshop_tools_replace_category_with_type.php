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
        Schema::table('workshop_tools', function (Blueprint $table) {
            if (!Schema::hasColumn('workshop_tools', 'type')) {
                $table->string('type')->after('name')->nullable()->index(); // Nullable initially to avoid errors with existing rows
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workshop_tools', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->unsignedBigInteger('category_id')->nullable();
        });
    }
};
