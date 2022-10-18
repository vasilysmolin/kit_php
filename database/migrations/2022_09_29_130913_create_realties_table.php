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
        Schema::create('realties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('profile_id');
            $table->unsignedBigInteger('external_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->string('state', 100)->default('new');
            $table->string('reason', 100)->nullable();
            $table->string('street')->nullable();
            $table->string('house')->nullable();
            $table->float('latitude', 10, 0)->nullable();
            $table->float('longitude', 10, 0)->nullable();
            $table->string('article')->nullable();
            $table->integer('sort')->nullable();
            $table->string('name')->nullable();
            $table->string('title', 1000)->nullable();
            $table->text('description')->nullable();
            $table->string('alias')->nullable()->unique();
            $table->integer('active')->unsigned()->default(1);
            $table->float('price', 10, 0)->nullable();
            $table->float('sale_price', 10, 0)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('category_id', 'FK_realty_category_id')
                ->references('id')
                ->on('catalog_ad_categories')
                ->onUpdate('RESTRICT')
                ->onDelete('CASCADE');
            $table->foreign('city_id', 'FK_city_realty')
                ->references('id')
                ->on('cities')
                ->onUpdate('RESTRICT')
                ->onDelete('CASCADE');
            $table->foreign('profile_id', 'FK_profile_realty')
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
        Schema::dropIfExists('realties');
    }
};
