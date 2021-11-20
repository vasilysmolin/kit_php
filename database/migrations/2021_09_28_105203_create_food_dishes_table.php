<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoodDishesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('food_dishes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->nullable();
            $table->unsignedBigInteger('restaurant_id');
            $table->string('alias', 255)->unique();
            $table->string('description', 255)->nullable();
            $table->float('price', 10, 2)->default(0);
            $table->float('salePrice', 10, 2)->default(0);
            $table->float('weight', 10, 2)->default(0);
            $table->integer('quantity')->default(0);
            $table->integer('sort')->nullable();
            $table->boolean('active')->default(0);
            $table->boolean('popular')->default(0);
            $table->boolean('sale')->default(0);
            $table->boolean('novetly')->default(0);
            $table->foreign('restaurant_id', 'FK_user_restaurant_food')
                ->references('id')
                ->on('food_restaurants')
                ->onUpdate('RESTRICT')
                ->onDelete('CASCADE');
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
        Schema::dropIfExists('restaurant_food');
    }
}
