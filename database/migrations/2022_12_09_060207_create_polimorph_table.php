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
        Schema::dropIfExists('catalog_ad_parameters');
        Schema::dropIfExists('catalog_parameters');
        Schema::dropIfExists('catalog_filters');
        Schema::rename('realty_filters', 'filters');
        Schema::rename('realty_parameters', 'parameters');
        Schema::table('filters', function (Blueprint $table) {
            $table->nullableMorphs('categoryable');
        });
        Schema::table('filter_parameters', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->nullableMorphs('itemable');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
};
