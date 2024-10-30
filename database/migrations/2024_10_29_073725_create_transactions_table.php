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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loan_application_id');
            $table->unsignedBigInteger('user_id');
            $table->decimal('amount', 15, 2); // amount of the transaction
            $table->string('payment_method')->nullable(); // e.g., bank, API, cash, etc.
            $table->string('transaction_reference')->nullable(); // for storing a reference from an external API
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->text('remarks')->nullable(); // additional information
            $table->timestamps();

            // Foreign keys
            $table->foreign('loan_application_id')->references('id')->on('loan_applications')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
