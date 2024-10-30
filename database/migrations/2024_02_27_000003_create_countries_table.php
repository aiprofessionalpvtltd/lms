<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountriesTable extends Migration
{
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100);
            $table->char('iso3', 3)->nullable();
            $table->char('numeric_code', 3)->nullable();
            $table->char('iso2', 2)->nullable();
            $table->string('phonecode')->nullable();
            $table->string('capital')->nullable();
            $table->string('currency')->nullable();
            $table->string('currency_name')->nullable();
            $table->string('currency_symbol')->nullable();
            $table->string('tld')->nullable();
            $table->string('native')->nullable();
            $table->string('region')->nullable();
            $table->bigInteger('region_id')->unsigned()->nullable();
            $table->string('subregion')->nullable();
            $table->bigInteger('subregion_id')->unsigned()->nullable();
            $table->string('nationality')->nullable();
            $table->text('timezones')->nullable();
            $table->text('translations')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('emoji')->nullable();
            $table->string('emojiU')->nullable();
            $table->timestamp('created_at')->nullable();;
            $table->timestamp('updated_at')->nullable();;
            $table->softDeletes();

            $table->tinyInteger('flag')->default(1);
            $table->string('wikiDataId')->nullable()->comment('Rapid API GeoDB Cities');
            $table->foreign('region_id')->references('id')->on('regions');
            $table->foreign('subregion_id')->references('id')->on('subregions');
            $table->index('region_id', 'country_continent');
            $table->index('subregion_id', 'country_subregion');
        });
    }

    public function down()
    {
        Schema::dropIfExists('countries');
    }
}
