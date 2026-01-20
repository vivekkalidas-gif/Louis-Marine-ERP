<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('journal_entries', 'name')) {
                $table->string('name')->after('journal_id');
            }
            if (!Schema::hasColumn('journal_entries', 'entity_id')) {
                $table->uuid('entity_id')->nullable()->after('state');
            }
            if (!Schema::hasColumn('journal_entries', 'entity_type')) {
                $table->string('entity_type')->nullable()->after('entity_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropColumn(['name', 'entity_id', 'entity_type']);
        });
    }
};
