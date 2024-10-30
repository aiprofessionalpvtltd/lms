<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDistrictsTable extends Migration
{
    public function up()
    {
        Schema::create('districts', function (Blueprint $table) {

            $table->bigIncrements('id')->unsigned();
            $table->string('name', 191);
            $table->string('lat', 191)->nullable()->default('NULL');
            $table->string('lng', 191)->nullable()->default('NULL');
            $table->timestamps();
            $table->tinyInteger('is_active')->default(1);
//            $table->foreign('province_id')->nullable()->references('id')->on('provinces')->onDelete('set null')->onUpdate('set null');
            $table->foreignId("province_id")->nullable()->constrained('provinces')->onDelete('set null')->onUpdate('set null');

        });
    }

    public function down()
    {
        Schema::dropIfExists('districts');
    }
}
