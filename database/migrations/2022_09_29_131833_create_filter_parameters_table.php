<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('filter_parameters', function (Blueprint $table) {
            $table->unsignedBigInteger('parameter_id')->nullable();
            $table->unsignedBigInteger('realty_id')->nullable();
            $table->foreign('parameter_id', 'FK_realty_filters_catalog_ad_parameters')
                ->references('id')
                ->on('realty_parameters')
                ->onUpdate('RESTRICT')
                ->onDelete('CASCADE');
            $table->foreign('realty_id', 'FK_realty_catalog_ad_parameters')
                ->references('id')
                ->on('realties')
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
        Schema::dropIfExists('filter_parameters');
    }
};
