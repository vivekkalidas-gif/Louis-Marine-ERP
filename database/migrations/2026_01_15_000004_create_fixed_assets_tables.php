<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Asset Categories (Defines GL Accounts)
        Schema::create('asset_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('depreciation_method')->default('SLM'); // SLM, WDV
            $table->integer('useful_life_years')->default(5);
            
            // GL Accounts integration
            $table->foreignUuid('asset_account_id')->constrained('chart_of_accounts');
            $table->foreignUuid('depreciation_expense_account_id')->constrained('chart_of_accounts');
            $table->foreignUuid('accumulated_depreciation_account_id')->constrained('chart_of_accounts');
            
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Fixed Assets Register
        Schema::create('fixed_assets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('category_id')->constrained('asset_categories');
            
            $table->string('asset_code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('location')->nullable();
            
            // Financials
            $table->date('purchase_date');
            $table->date('capitalization_date'); // When depreciation starts
            $table->decimal('purchase_cost', 15, 2);
            $table->decimal('salvage_value', 15, 2)->default(0);
            
            // Depreciation Settings (Override Category default if needed)
            $table->string('depreciation_method')->default('SLM'); 
            $table->integer('useful_life_years');
            
            // Tracking
            $table->string('status')->default('active'); // active, sold, disposed, written_off
            $table->decimal('accumulated_depreciation', 15, 2)->default(0);
            $table->decimal('current_value', 15, 2); // Net Book Value
            
            $table->foreignUuid('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Depreciation Schedules / History
        Schema::create('depreciation_schedules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('fixed_asset_id')->constrained('fixed_assets')->onDelete('cascade');
            
            $table->date('schedule_date'); // depreciation run date
            $table->decimal('amount', 15, 2);
            $table->decimal('net_book_value_before', 15, 2);
            $table->decimal('net_book_value_after', 15, 2);
            
            $table->boolean('is_posted')->default(false);
            $table->foreignUuid('journal_entry_id')->nullable()->constrained('journal_entries');
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('depreciation_schedules');
        Schema::dropIfExists('fixed_assets');
        Schema::dropIfExists('asset_categories');
    }
};
