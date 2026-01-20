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
        Schema::table('service_jobs', function (Blueprint $table) {
            // Delivery Note fields for Workshop Service jobs
            $table->string('delivery_note_number')->nullable()->unique()->after('job_number');
            $table->string('delivered_by')->nullable()->after('location_details');
            $table->string('delivery_contact_person')->nullable()->after('delivered_by');
            $table->string('delivery_contact_number')->nullable()->after('delivery_contact_person');
            $table->text('delivery_notes')->nullable()->after('delivery_contact_number');
            $table->timestamp('delivery_received_at')->nullable()->after('delivery_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_jobs', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_note_number',
                'delivered_by',
                'delivery_contact_person',
                'delivery_contact_number',
                'delivery_notes',
                'delivery_received_at'
            ]);
        });
    }
};
