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
        Schema::table('recoveries', function (Blueprint $table) {
            $table->boolean('is_early_settlement')->default(false);
            $table->string('remaining_amount')->default(false);
            $table->string('percentage')->default(false);
            $table->string('erc_amount')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recoveries', function (Blueprint $table) {
            //
        });
    }
};
