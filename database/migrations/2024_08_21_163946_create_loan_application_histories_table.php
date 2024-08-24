<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('loan_application_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loan_application_id');
            $table->enum('status', ['pending', 'accepted', 'rejected']);
            $table->unsignedBigInteger('from_user_id');
            $table->unsignedBigInteger('from_role_id');
            $table->unsignedBigInteger('to_user_id');
            $table->unsignedBigInteger('to_role_id');
            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->foreign('from_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('from_role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('to_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('to_role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('loan_application_id')->references('id')->on('loan_applications')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_application_histories');
    }
};
