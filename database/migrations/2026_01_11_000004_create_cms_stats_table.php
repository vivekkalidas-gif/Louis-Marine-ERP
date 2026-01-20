<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_stats', function (Blueprint $table) {
            $table->id();
            $table->string('label'); // e.g., "Years in Business"
            $table->string('value'); // e.g., "15+"
            $table->string('icon')->nullable(); // SVG code or icon class
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_stats');
    }
};
