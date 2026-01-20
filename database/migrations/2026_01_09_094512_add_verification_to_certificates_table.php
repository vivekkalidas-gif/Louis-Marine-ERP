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
        Schema::table('certificates', function (Blueprint $table) {
            $table->uuid('verifier_id')->nullable()->after('issued_by');
            $table->timestamp('verified_at')->nullable()->after('verifier_id');
            $table->text('verification_notes')->nullable()->after('verified_at');
            
            $table->index('verifier_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropForeign(['verifier_id']);
            $table->dropColumn(['verifier_id', 'verified_at', 'verification_notes']);
        });
    }
};
