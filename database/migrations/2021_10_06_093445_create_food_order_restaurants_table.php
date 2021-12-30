<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoodOrderRestaurantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('food_order_restaurants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('restaurant_id');
            $table->integer('status')->default(1);
            $table->integer('delivery_price')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('order_id', 'FK_food_order_restaurants_orders')
                ->references('id')
                ->on('food_orders')
                ->onUpdate('RESTRICT')
                ->onDelete('CASCADE');
            $table->foreign('restaurant_id', 'FK_food_order_restaurants_restaurant')
                ->references('id')
                ->on('food_restaurants')
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
        Schema::dropIfExists('food_order_restaurants');
    }
}
