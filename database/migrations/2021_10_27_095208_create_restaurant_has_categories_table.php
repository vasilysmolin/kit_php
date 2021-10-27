<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestaurantHasCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurant_has_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id', 'FK_category_has_id')
                ->references('id')
                ->on('category_restaurants')
                ->onUpdate('RESTRICT')
                ->onDelete('RESTRICT');
            $table->unsignedBigInteger('restaurant_id');
            $table->foreign('restaurant_id', 'FK_restaurants_has_id')
                ->references('id')
                ->on('restaurants')
                ->onUpdate('RESTRICT')
                ->onDelete('RESTRICT');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('restaurant_has_categories');
    }
}
