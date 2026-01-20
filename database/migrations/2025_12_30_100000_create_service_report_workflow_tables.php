<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Service Types Master Table
        // 1. Service Types Master Table - Update existing table
        if (Schema::hasTable('service_types')) {
             Schema::table('service_types', function (Blueprint $table) {
                if (!Schema::hasColumn('service_types', 'default_price')) {
                    $table->decimal('default_price', 10, 2)->default(0);
                }
                if (!Schema::hasColumn('service_types', 'description')) {
                    $table->text('description')->nullable();
                }
                if (!Schema::hasColumn('service_types', 'status')) {
                    $table->string('status')->default('active'); // active, inactive
                }
            });
        }

        // Seed Default Service Types (Initial Data) - using updateOrInsert to prevent duplicates
        $services = [
            ['name' => 'Visual Inspection', 'code' => 'VISUAL', 'default_price' => 50],
            ['name' => 'Refilling', 'code' => 'REFILL', 'default_price' => 150],
            ['name' => 'Hydrotest', 'code' => 'HYDRO', 'default_price' => 300],
            ['name' => 'Painting', 'code' => 'PAINT', 'default_price' => 100],
            ['name' => 'Service & Maintenance', 'code' => 'SERVICE', 'default_price' => 200],
        ];

        foreach ($services as $service) {
            DB::table('service_types')->updateOrInsert(
                ['code' => $service['code']],
                [
                    'name' => $service['name'],
                    'default_price' => $service['default_price'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }

        // 2. Service Report Items (Services performed)
        if (!Schema::hasTable('service_report_items')) {
            Schema::create('service_report_items', function (Blueprint $table) {
                $table->id();
                $table->foreignUuid('service_report_id')->constrained('service_reports')->onDelete('cascade');
                $table->foreignId('service_type_id')->constrained('service_types');
                $table->string('description')->nullable(); // Optional override or extra detail
                $table->decimal('price', 10, 2)->default(0); // Price snapshot
                $table->timestamps();
            });
        }

        // 3. Service Report Charges (Non-stock parts/items)
        if (!Schema::hasTable('service_report_charges')) {
            Schema::create('service_report_charges', function (Blueprint $table) {
                $table->id();
                $table->foreignUuid('service_report_id')->constrained('service_reports')->onDelete('cascade');
                $table->string('item_description');
                $table->integer('quantity')->default(1);
                $table->decimal('unit_price', 10, 2)->default(0);
                $table->decimal('total_price', 10, 2)->default(0);
                $table->timestamps();
            });
        }

        // 4. Update Service Reports Table
        Schema::table('service_reports', function (Blueprint $table) {
            $table->decimal('total_service_amount', 10, 2)->default(0)->after('hours_worked');
            // Ensure status has enough length or updated enum if strictly defined elsewhere (it's string default 'Draft' currently)
        });
    }

    public function down(): void
    {
        Schema::table('service_reports', function (Blueprint $table) {
            $table->dropColumn('total_service_amount');
        });

        Schema::dropIfExists('service_report_charges');
        Schema::dropIfExists('service_report_items');
        Schema::dropIfExists('service_types');
    }
};
