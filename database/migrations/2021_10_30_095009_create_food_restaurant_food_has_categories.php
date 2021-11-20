<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoodRestaurantFoodHasCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('food_restaurant_food_has_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id', 'FK_category_food_has_id')
                ->references('id')
                ->on('food_dishes_categories')
                ->onUpdate('RESTRICT')
                ->onDelete('RESTRICT');
            $table->unsignedBigInteger('restaurant_food_id');
            $table->foreign('restaurant_food_id', 'FK_restaurants_food_has_id')
                ->references('id')
                ->on('food_dishes')
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
        Schema::dropIfExists('restaurant_food_has_categories');
    }
}
