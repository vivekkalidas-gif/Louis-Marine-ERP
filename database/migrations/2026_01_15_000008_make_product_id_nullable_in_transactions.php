<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('inventory_transactions', function (Blueprint $table) {
            // Make product_id nullable
            $table->uuid('product_id')->nullable()->change();
        });
    }

    public function down()
    {
        // Cannot revert easily without data loss risk if nulls exist
    }
};
