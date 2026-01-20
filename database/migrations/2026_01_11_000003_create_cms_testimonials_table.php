<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('client_name');
            $table->string('company_name')->nullable();
            $table->text('testimonial_text');
            $table->string('client_photo')->nullable();
            $table->integer('rating')->default(5); // 1-5 stars
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_testimonials');
    }
};
