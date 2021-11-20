<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoodRestaurantHasCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('food_restaurant_has_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id', 'FK_category_has_id')
                ->references('id')
                ->on('food_category_restaurants')
                ->onUpdate('RESTRICT')
                ->onDelete('CASCADE');
            $table->unsignedBigInteger('restaurant_id');
            $table->foreign('restaurant_id', 'FK_restaurants_has_id')
                ->references('id')
                ->on('food_restaurants')
                ->onUpdate('RESTRICT')
                ->onDelete('CASCADE');
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
