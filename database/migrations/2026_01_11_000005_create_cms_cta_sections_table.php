<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_cta_sections', function (Blueprint $table) {
            $table->id();
            $table->string('section_key')->unique(); // e.g., 'footer_cta', 'mid_page_cta'
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_link')->nullable();
            $table->string('background_color')->default('#0b1c3c');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_cta_sections');
    }
};
