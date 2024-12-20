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
        Schema::table('products', function (Blueprint $table) {
            // Adding vendor_id and vendor_product_id as foreign keys
            $table->unsignedBigInteger('vendor_id')->nullable()->after('id');
            $table->unsignedBigInteger('vendor_product_id')->nullable()->after('vendor_id');

            // Adding foreign key constraints
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('set null');
            $table->foreign('vendor_product_id')->references('id')->on('vendor_products')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Dropping foreign key constraints
            $table->dropForeign(['vendor_id']);
            $table->dropForeign(['vendor_product_id']);

            // Dropping the columns
            $table->dropColumn(['vendor_id', 'vendor_product_id']);
        });
    }
};
