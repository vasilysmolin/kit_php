<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoodOrderFoodTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('food_order_dishes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_restaurant_id');
            $table->unsignedBigInteger('restaurant_dishes_id');
            $table->float('price', 10, 0)->unsigned();
            $table->float('salePrice', 10, 0)->unsigned();
            $table->integer('quantity')->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('food_order_dishes');
    }
}
