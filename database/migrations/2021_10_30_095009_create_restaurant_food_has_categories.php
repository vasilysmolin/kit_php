<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestaurantFoodHasCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurant_food_has_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id', 'FK_category_food_has_id')
                ->references('id')
                ->on('category_food')
                ->onUpdate('RESTRICT')
                ->onDelete('RESTRICT');
            $table->unsignedBigInteger('restaurant_food_id');
            $table->foreign('restaurant_food_id', 'FK_restaurants_food_has_id')
                ->references('id')
                ->on('restaurant_foods')
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
