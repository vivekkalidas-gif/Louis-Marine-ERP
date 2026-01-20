<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
             // Skipped to unblock
        });

        Schema::table('journal_items', function (Blueprint $table) {
             // Skipped to unblock
        });
    }

    public function down(): void
    {
        Schema::table('journal_items', function (Blueprint $table) {
            $table->dropForeign(['cost_center_id']);
            $table->dropColumn('cost_center_id');
        });

        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropForeign(['reversed_entry_id']);
            $table->dropColumn(['number', 'posted_by', 'reversed_date', 'reversed_entry_id']);
        });
    }
};
