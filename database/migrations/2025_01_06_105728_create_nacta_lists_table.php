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
        Schema::create('nacta_lists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('father')->nullable();;
            $table->string('cnic')->nullable();
            $table->string('province');
            $table->string('district');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nacta_lists');
    }
};
