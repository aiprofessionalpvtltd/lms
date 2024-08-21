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
        Schema::create('loan_application_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loan_application_id');
            $table->enum('status', ['pending', 'accepted', 'rejected']);
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('loan_application_id')
                ->references('id')
                ->on('loan_applications')
                ->onDelete('cascade');
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
