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
        // Drop table and recreate to avoid foreign key issues during refactor
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('tool_requests');
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        Schema::create('tool_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id'); // Removed constrained()->cascadeOnDelete() to debug
            
            // New relationship to WorkshopTool
            $table->foreignUuid('workshop_tool_id')->nullable(); // Removed constrained() to debug
            
            $table->string('status')->default('pending'); // pending, issued, rejected, returned
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();
            
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tool_requests', function (Blueprint $table) {
            $table->dropForeign(['workshop_tool_id']);
            $table->dropColumn('workshop_tool_id');
            
            $table->foreignId('equipment_item_id')->constrained('equipment_items')->cascadeOnDelete();
        });
    }
};
