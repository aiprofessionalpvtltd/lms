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
        Schema::create('loan_application_products', function (Blueprint $table) {
            $table->id();
            $table->enum('request_for', ['product', 'loan']);
            $table->unsignedBigInteger('loan_application_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('loan_duration_id');

            $table->decimal('loan_amount', 15, 2);
            $table->decimal('down_payment_percentage', 5, 2);
            $table->decimal('processing_fee_percentage', 5, 2);
            $table->decimal('interest_rate_percentage', 5, 2);
            $table->decimal('financed_amount', 15, 2);
            $table->decimal('processing_fee_amount', 15, 2);
            $table->decimal('down_payment_amount', 15, 2);
            $table->decimal('total_upfront_payment', 15, 2);
            $table->decimal('disbursement_amount', 15, 2);
            $table->decimal('total_interest_amount', 15, 2);
            $table->decimal('total_repayable_amount', 15, 2);
            $table->decimal('monthly_installment_amount', 15, 2);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('loan_application_id')->references('id')->on('loan_applications')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('loan_duration_id')->references('id')->on('loan_durations')->onDelete('cascade');
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
