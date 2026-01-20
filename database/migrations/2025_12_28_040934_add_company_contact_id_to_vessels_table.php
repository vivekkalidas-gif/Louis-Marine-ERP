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
        Schema::table('vessels', function (Blueprint $table) {
            if (!Schema::hasColumn('vessels', 'company_contact_id')) {
                 $table->foreignUuid('company_contact_id')->nullable()->constrained('customer_contacts')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vessels', function (Blueprint $table) {
            $table->dropForeign(['company_contact_id']);
            $table->dropColumn('company_contact_id');
        });
    }
};
