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
        Schema::create('user_profile_tracking', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');

            // Boolean fields
            $table->boolean('is_registration')->default(false);
            $table->boolean('is_kyc')->default(false);
            $table->boolean('is_profile')->default(false);
            $table->boolean('is_reference')->default(false);
            $table->boolean('is_utility')->default(false);
            $table->boolean('is_bank_statement')->default(false);
            $table->boolean('is_address_proof')->default(false);
            $table->boolean('is_eligibility')->default(false);
            $table->integer('score')->default(0);

            $table->timestamps();

            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profile_trackings');
    }
};
