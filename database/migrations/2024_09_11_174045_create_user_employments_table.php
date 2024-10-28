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
        Schema::create('user_employments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('employment_status_id');
            $table->unsignedBigInteger('income_source_id');
            $table->unsignedBigInteger('job_title_id');
            $table->string('current_employer')->nullable();
            $table->string('employment_duration')->nullable();
            $table->decimal('gross_income', 15, 2)->nullable(); // For gross income
            $table->decimal('net_income', 15, 2)->nullable();   // For net income
            $table->unsignedBigInteger('existing_loans_id');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('employment_status_id')
                ->references('id')
                ->on('employment_statuses')
                ->onDelete('cascade');

            $table->foreign('income_source_id')
                ->references('id')
                ->on('income_sources')
                ->onDelete('cascade');

            $table->foreign('job_title_id')
                ->references('id')
                ->on('job_titles')
                ->onDelete('cascade');

            $table->foreign('existing_loans_id')
                ->references('id')
                ->on('existing_loans')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_employments');
    }
};
