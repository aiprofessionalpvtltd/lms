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
            $table->integer('overdue_days')->after('amount');
            $table->decimal('penalty_fee',8, 2)->after('overdue_days');
            $table->decimal('total_amount',8, 2)->after('penalty_fee');

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
