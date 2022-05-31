<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCatalogFiltersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog_filters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('name')->nullable();
            $table->string('type')->default('select');
            $table->string('alias')->nullable()->unique();
            $table->integer('sort')->unsigned()->nullable();
            $table->integer('active')->nullable()->default(1);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('category_id', 'FK_catalog_filters_category_id')
                ->references('id')
                ->on('catalog_ad_categories')
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
        Schema::dropIfExists('catalog_filters');
    }
}
