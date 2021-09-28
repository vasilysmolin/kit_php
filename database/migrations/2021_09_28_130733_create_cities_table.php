<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('region_id');
            $table->string('name');
            $table->string('prepositionalName');
            $table->string('alias');
            $table->float('coordX', 10, 0);
            $table->float('coordY', 10, 0);
            $table->integer('isMetro')->unsigned()->default(0);
            $table->integer('isDistrict')->unsigned()->default(1);
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->string('seoH1')->nullable();
            $table->text('text')->nullable();
            $table->integer('active')->unsigned()->default(1);
            $table->timestamps();
            $table->foreign('region_id', 'FK_region_id_cities')
                ->references('id')
                ->on('regions')
                ->onUpdate('RESTRICT')
                ->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cities');
    }
}
