<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCatalogAdParameterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog_ad_parameters', function (Blueprint $table) {
            $table->unsignedBigInteger('parameter_id')->nullable();
            $table->unsignedBigInteger('ad_id')->nullable();
            $table->foreign('parameter_id', 'FK_catalog_filters_catalog_ad_parameters')
                ->references('id')
                ->on('catalog_parameters')
                ->onUpdate('RESTRICT')
                ->onDelete('CASCADE');
            $table->foreign('ad_id', 'FK_catalog_ads_catalog_ad_parameters')
                ->references('id')
                ->on('catalog_ads')
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
        Schema::dropIfExists('catalog_ad_parameters');
    }
}
