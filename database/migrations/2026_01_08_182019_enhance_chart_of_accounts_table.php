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
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->uuid('parent_id')->nullable()->after('id');
            $table->text('description')->nullable()->after('name');
            $table->boolean('is_active')->default(true)->after('is_reconcilable');
            
            // Self-referential FK
            $table->foreign('parent_id')->references('id')->on('chart_of_accounts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'description', 'is_active']);
        });
    }
};
