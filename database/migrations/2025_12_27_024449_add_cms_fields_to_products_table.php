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
        Schema::table('products', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('name');
            $table->text('short_description')->nullable()->after('description');
            $table->json('specifications')->nullable()->after('short_description');
            $table->boolean('is_published')->default(false)->after('current_stock');
            $table->integer('sort_order')->default(0)->after('is_published');
            $table->json('image_paths')->nullable()->after('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['slug', 'short_description', 'specifications', 'is_published', 'sort_order', 'image_paths']);
        });
    }
};
