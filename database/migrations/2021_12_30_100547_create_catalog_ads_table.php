<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCatalogAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog_ads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('profile_id');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->string('article')->nullable();
            $table->integer('sort')->nullable();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('alias')->nullable()->unique();
            $table->integer('active')->unsigned()->default(1);
            $table->float('price', 10, 0)->nullable();
            $table->float('sale_price', 10, 0)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('category_id', 'FK_catalog_ads_category_id')
                ->references('id')
                ->on('catalog_ad_categories')
                ->onUpdate('RESTRICT')
                ->onDelete('CASCADE');
            $table->foreign('city_id', 'FK_city_catalog_ads')
                ->references('id')
                ->on('cities')
                ->onUpdate('RESTRICT')
                ->onDelete('CASCADE');
            $table->foreign('profile_id', 'FK_profile_catalog_ads')
                ->references('id')
                ->on('profiles')
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
        Schema::dropIfExists('catalog_ads');
    }
}
