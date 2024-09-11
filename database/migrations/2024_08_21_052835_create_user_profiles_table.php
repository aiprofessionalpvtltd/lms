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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->unsignedBigInteger('gender_id');
            $table->unsignedBigInteger('marital_status_id');
            $table->unsignedBigInteger('nationality_id');

            $table->string('first_name');
            $table->string('last_name');
            $table->string('father_name');
            $table->string('photo')->nullable();
            $table->string('cnic_front');
            $table->string('cnic_back');
            $table->string('cnic_no')->unique();
            $table->date('issue_date')->nullable();
            $table->date('expire_date')->nullable();
            $table->date('dob');
            $table->string('mobile_no')->unique();
            $table->string('alternate_mobile_no');
            $table->text('permanent_address');
            $table->text('current_address');
            $table->text('current_address_duration');

            $table->timestamps();

            // Foreign key relationships with constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('gender_id')->references('id')->on('genders')->onDelete('restrict');
            $table->foreign('marital_status_id')->references('id')->on('marital_statuses')->onDelete('restrict');
            $table->foreign('nationality_id')->references('id')->on('nationalities')->onDelete('restrict');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
