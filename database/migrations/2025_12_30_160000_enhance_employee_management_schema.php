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
        // 1. Enhance Users Table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'mobile')) {
                $table->string('mobile')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('mobile');
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('password');
            }
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('updated_at');
            }
        });

        // 2. Enhance Employee Details Table
        Schema::table('employee_details', function (Blueprint $table) {
            // Identity
            $table->string('nationality')->nullable()->after('employee_code');
            $table->date('dob')->nullable()->after('nationality');
            $table->string('gender')->nullable()->after('dob'); // male, female, other

            // Employment
            $table->string('department')->nullable()->after('designation');
            $table->string('employment_type')->default('Permanent')->after('department'); // Permanent, Contract, Temporary
            $table->string('work_location')->nullable()->after('employment_type');
            $table->foreignUuid('reporting_manager_id')->nullable()->constrained('users')->nullOnDelete()->after('user_id');
            
            // Financial
            $table->string('payment_mode')->default('Bank Transfer')->after('bank_details'); // Bank Transfer, Cash, Cheque
            
            // Status
            $table->string('status')->default('Active')->after('payment_mode'); // Active, On Leave, Resigned, Terminated
        });

        // 3. Create Employee Documents Table
        Schema::create('employee_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            
            $table->string('document_type'); // passport, visa, qid, medical, other
            $table->string('document_number')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('file_path');
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_documents');

        Schema::table('employee_details', function (Blueprint $table) {
            $table->dropForeign(['reporting_manager_id']);
            $table->dropColumn([
                'nationality', 'dob', 'gender',
                'department', 'employment_type', 'work_location', 'reporting_manager_id',
                'payment_mode', 'status'
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['mobile', 'avatar', 'is_active', 'last_login_at']);
        });
    }
};
