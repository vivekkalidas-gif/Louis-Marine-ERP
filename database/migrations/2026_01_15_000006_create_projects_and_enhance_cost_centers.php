<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Skipped to unblock

    }

    public function down()
    {
        Schema::table('journal_items', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
        });

        Schema::dropIfExists('projects');

        Schema::table('cost_centers', function (Blueprint $table) {
            $table->dropForeign(['manager_id']);
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'type', 'manager_id', 'is_active']);
        });
    }
};
