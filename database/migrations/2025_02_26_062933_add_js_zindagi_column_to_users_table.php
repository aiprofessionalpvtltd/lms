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
        Schema::table('users', function (Blueprint $table) {
           $table->boolean('is_zindagi_verified')->after('is_nacta_clear')->default(0);
           $table->boolean('is_account_opened')->after('is_zindagi_verified')->default(0);
           $table->date('account_opening_date')->after('is_account_opened')->nullable();
           $table->string('zindagi_trace_no')->after('account_opening_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
