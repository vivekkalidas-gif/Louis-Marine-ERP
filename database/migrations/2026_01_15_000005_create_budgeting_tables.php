<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Budgets Header
        Schema::create('budgets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name'); // e.g. "FY 2026 Master Budget"
            $table->string('fiscal_year'); // e.g. "2026"
            $table->string('period_type')->default('monthly'); // monthly, quarterly, yearly
            
            // Scope (Company, Branch, Department - optional for now, keeping simple)
            // $table->foreignUuid('branch_id')->nullable(); 

            $table->string('status')->default('draft'); // draft, approved, closed
            $table->foreignUuid('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Budget Items (Lines)
        Schema::create('budget_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('budget_id')->constrained('budgets')->onDelete('cascade');
            $table->foreignUuid('chart_of_account_id')->constrained('chart_of_accounts');
            
            $table->date('period_date'); // E.g. 2026-01-01 for Jan 2026 bucket
            $table->decimal('amount', 15, 2)->default(0);
            
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            $table->unique(['budget_id', 'chart_of_account_id', 'period_date'], 'budget_item_unique');
        });
        
        // 3. Forecasts
        Schema::create('forecasts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name'); // "Q2 Forecast"
            $table->foreignUuid('base_budget_id')->nullable()->constrained('budgets'); // Based on which budget?
            
            $table->string('version')->default('v1');
            $table->date('start_date');
            $table->date('end_date');
            
            $table->foreignUuid('created_by')->constrained('users');
            $table->timestamps();
        });

        Schema::create('forecast_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('forecast_id')->constrained('forecasts')->onDelete('cascade');
            $table->foreignUuid('chart_of_account_id')->constrained('chart_of_accounts');
            
             $table->date('period_date');
             $table->decimal('amount', 15, 2)->default(0);
             
             $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('forecast_items');
        Schema::dropIfExists('forecasts');
        Schema::dropIfExists('budget_items');
        Schema::dropIfExists('budgets');
    }
};
