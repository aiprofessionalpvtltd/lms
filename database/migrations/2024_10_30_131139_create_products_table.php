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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('detail')->nullable();
            $table->decimal('processing_fee', 8, 2)->default(0.00);
            $table->decimal('interest_rate', 5, 2)->default(0.00);
            $table->unsignedBigInteger('province_id');
            $table->unsignedBigInteger('district_id');
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys (assuming `provinces` and `districts` tables already exist)
            $table->foreign('province_id')->references('id')->on('provinces')->onDelete('cascade');
            $table->foreign('district_id')->references('id')->on('districts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
