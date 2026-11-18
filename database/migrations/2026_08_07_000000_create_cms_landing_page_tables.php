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
        if (!Schema::hasTable('cms_hero_slides')) {
            Schema::create('cms_hero_slides', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('subtitle')->nullable();
                $table->text('description')->nullable();
                $table->string('background_image')->nullable();
                $table->string('background_video_url')->nullable();
                $table->string('cta_text_1')->nullable();
                $table->string('cta_link_1')->nullable();
                $table->string('cta_text_2')->nullable();
                $table->string('cta_link_2')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('cms_features')) {
            Schema::create('cms_features', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('icon')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('cms_testimonials')) {
            Schema::create('cms_testimonials', function (Blueprint $table) {
                $table->id();
                $table->string('client_name');
                $table->string('company_name')->nullable();
                $table->text('testimonial_text');
                $table->string('client_photo')->nullable();
                $table->integer('rating')->default(5);
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('cms_stats')) {
            Schema::create('cms_stats', function (Blueprint $table) {
                $table->id();
                $table->string('label');
                $table->string('value');
                $table->string('icon')->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('cms_cta_sections')) {
            Schema::create('cms_cta_sections', function (Blueprint $table) {
                $table->id();
                $table->string('section_key')->nullable();
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('button_text')->nullable();
                $table->string('button_link')->nullable();
                $table->string('background_color')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms_cta_sections');
        Schema::dropIfExists('cms_stats');
        Schema::dropIfExists('cms_testimonials');
        Schema::dropIfExists('cms_features');
        Schema::dropIfExists('cms_hero_slides');
    }
};
