<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProvincesTable extends Migration
{
    public function up()
    {
        Schema::create('provinces', function (Blueprint $table) {

            $table->bigIncrements('id')->unsigned();
            $table->string('name', 191);
            $table->string('urdu', 191);
            $table->string('lat', 191)->nullable()->default('NULL');
            $table->string('lng', 191)->nullable()->default('NULL');
            $table->timestamps();
            $table->tinyInteger('is_active')->default(1);
            $table->unsignedBigInteger('country_id')->default(168);
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');

         });
    }

    public function down()
    {
        Schema::dropIfExists('provinces');
    }
}
