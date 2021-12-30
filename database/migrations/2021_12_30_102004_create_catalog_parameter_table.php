<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCatalogParameterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog_parameters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('filter_id')->nullable();
            $table->string('value')->nullable();
            $table->integer('sort')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('filter_id', 'FK_catalog_parameters_category_id')
                ->references('id')
                ->on('catalog_filters')
                ->onUpdate('RESTRICT')
                ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('catalog_parameters');
    }
}
