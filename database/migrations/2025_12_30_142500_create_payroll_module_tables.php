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
        // 1. Employee Details (Extends User)
        Schema::create('employee_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->string('employee_code')->unique();
            $table->string('designation')->nullable();
            $table->date('join_date')->nullable();
            
            // Salary Structure
            $table->decimal('basic_salary', 15, 2)->default(0);
            $table->decimal('hourly_rate', 10, 2)->default(0); // For OT
            
            // Allowances
            $table->decimal('allowance_transport', 15, 2)->default(0);
            $table->decimal('allowance_food', 15, 2)->default(0);
            $table->decimal('allowance_other', 15, 2)->default(0);
            
            $table->text('bank_details')->nullable(); // JSON or Text
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Overtime Records
        Schema::create('overtime_records', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade'); // Employee
            $table->date('date');
            $table->decimal('hours', 5, 2);
            
            // Optional Link to Job for Costing
            $table->foreignUuid('job_id')->nullable()->constrained('service_jobs')->nullOnDelete();
            
            $table->string('reason')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->foreignUuid('approved_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Payroll Batches (Monthly)
        Schema::create('payrolls', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('month'); // YYYY-MM
            $table->string('status')->default('draft'); // draft, finalized, paid
            $table->date('processed_date')->nullable();
            
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->foreignUuid('processed_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
        });

        // 4. Payroll Entries (Employee Specific)
        Schema::create('payroll_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('payroll_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained(); // Employee
            
            // Snapshot of calculations
            $table->decimal('basic_salary', 15, 2);
            $table->decimal('allowances_total', 15, 2);
            
            $table->decimal('overtime_hours', 8, 2)->default(0);
            $table->decimal('overtime_amount', 15, 2)->default(0);
            
            $table->decimal('deductions_total', 15, 2)->default(0);
            
            $table->decimal('gross_salary', 15, 2);
            $table->decimal('net_salary', 15, 2);
            
            $table->string('payment_status')->default('pending');
            $table->date('payment_date')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_entries');
        Schema::dropIfExists('payrolls');
        Schema::dropIfExists('overtime_records');
        Schema::dropIfExists('employee_details');
    }
};
