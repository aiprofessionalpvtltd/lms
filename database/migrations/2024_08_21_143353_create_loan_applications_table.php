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
        Schema::create('loan_applications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->decimal('loan_amount', 15, 2)->nullable();
            $table->unsignedBigInteger('loan_duration_id')->nullable();
            $table->unsignedBigInteger('product_service_id')->nullable();
            $table->unsignedBigInteger('loan_purpose_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('approved_by')->nullable();

            $table->text('address')->nullable();
            $table->string('reference_contact_1')->nullable();
            $table->string('reference_contact_2')->nullable();
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->json('documents')->nullable(); // To store multiple document paths
            $table->boolean('is_completed')->default(false); // To store multiple document paths
            $table->boolean('is_submitted')->default(false); // To store multiple document paths
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('loan_duration_id')->references('id')->on('loan_durations')->onDelete('cascade');
            $table->foreign('product_service_id')->references('id')->on('product_services')->onDelete('cascade');
            $table->foreign('loan_purpose_id')->references('id')->on('loan_purposes')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_applications');
    }
};
