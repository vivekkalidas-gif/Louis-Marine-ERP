<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('employee_ledger_entries')) {
            Schema::create('employee_ledger_entries', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
                $table->date('date');
                $table->string('type'); // 'advance_given', 'repayment', 'salary_credit', 'other'
                $table->decimal('amount', 12, 2); 
                $table->decimal('debit', 12, 2)->default(0); // Money User Owes (Advance Given)
                $table->decimal('credit', 12, 2)->default(0); // Money User Paid Back (Deduction)
                $table->string('description')->nullable();
                $table->string('reference')->nullable(); // Link to payroll_id or transaction_id
                $table->foreignUuid('created_by')->nullable()->constrained('users');
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('employee_ledger_entries');
    }
};