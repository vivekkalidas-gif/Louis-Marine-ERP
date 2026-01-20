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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('type')->default('local'); // Added to match seeder
            $table->string('status')->default('active'); // Added to match seeder
            $table->string('category')->nullable(); // Added to match seeder
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable(); // Added
            $table->text('address')->nullable();
            $table->string('tax_id')->nullable(); // Added
            $table->foreignId('currency_id')->nullable(); // Added
            $table->string('incoterm')->nullable(); // Added
            $table->string('payment_terms')->nullable(); // Added
            $table->integer('rating')->default(0); // Added
            $table->foreignUuid('created_by')->nullable(); // Added
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
