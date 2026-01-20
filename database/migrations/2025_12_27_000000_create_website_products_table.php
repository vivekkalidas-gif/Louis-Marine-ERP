<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Website Categories
        Schema::create('website_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('image_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Website Products (Strictly for display, no inventory fields)
        Schema::create('website_products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('website_category_id')->nullable()->constrained()->nullOnDelete();
            
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            
            $table->string('image_path')->nullable(); // Main image
            $table->json('gallery_images')->nullable(); // Array of slides
            $table->json('specifications')->nullable(); // Key-value pairs for technical specs
            
            // SEO Meta (Requested Requirement)
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('seo_keywords')->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('website_products');
        Schema::dropIfExists('website_categories');
    }
};
