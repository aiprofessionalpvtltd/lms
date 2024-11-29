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
        Schema::create('loan_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loan_application_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('document_type_id');
            $table->string('path'); // Path to the document file
            $table->timestamps();

            $table->foreign('loan_application_id')
                ->references('id')
                ->on('loan_applications');

            $table->foreign('user_id')
                ->references('id')
                ->on('users');

            $table->foreign('document_type_id')
                ->references('id')
                ->on('document_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_attachments');
    }
};
