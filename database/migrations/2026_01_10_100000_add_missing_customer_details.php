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
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'email')) {
                $table->string('email')->nullable()->after('company_name');
            }
            if (!Schema::hasColumn('customers', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            if (!Schema::hasColumn('customers', 'vat_number')) {
                $table->string('vat_number')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('customers', 'address')) {
                $table->text('address')->nullable()->after('vat_number');
            }
            if (!Schema::hasColumn('customers', 'created_by')) {
                $table->foreignUuid('created_by')->nullable()->constrained('users');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['email', 'phone', 'vat_number', 'address', 'created_by']);
        });
    }
};
