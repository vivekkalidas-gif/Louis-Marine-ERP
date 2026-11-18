<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('petty_cash_entries')) {
            Schema::create('petty_cash_entries', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->date('date');
                $table->string('type'); // 'deposit' (In), 'expense' (Out)
                $table->decimal('amount', 12, 2);
                $table->string('category')->nullable(); // Food, Travel, Office
                $table->string('description');
                $table->string('receipt_path')->nullable();
                $table->foreignUuid('created_by')->nullable()->constrained('users');
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('petty_cash_entries');
    }
};