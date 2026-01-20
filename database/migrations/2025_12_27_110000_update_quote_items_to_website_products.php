<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('quote_request_items', function (Blueprint $table) {
            // Drop valid FK if exists (ignoring if not to be safe, but migration should be precise)
            // Using array syntax for dropping foreign key
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');

            // Add new column
            $table->foreignUuid('website_product_id')->nullable()->constrained('website_products')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('quote_request_items', function (Blueprint $table) {
            $table->dropForeign(['website_product_id']);
            $table->dropColumn('website_product_id');
            $table->foreignUuid('product_id')->nullable()->constrained('products')->onDelete('set null');
        });
    }
};
