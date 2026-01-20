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
        Schema::create('quotations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('service_job_id')->nullable()->constrained()->nullOnDelete();
            $table->string('quotation_number')->unique(); // QTE-YYYY-0001
            $table->date('date');
            $table->date('valid_until');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax', 15, 2)->default(0); // VAT
            $table->decimal('total', 15, 2)->default(0);
            $table->string('status')->default('DRAFT'); // DRAFT, SENT, ACCEPTED, REJECTED, CONVERTED
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
