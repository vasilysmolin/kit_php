<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoodOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('food_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unsigned()->nullable();
            $table->unsignedBigInteger('profile_id')->unsigned()->nullable();
            $table->string('name');
            $table->string('surname')->nullable();
            $table->string('patronymic')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('city_id')->unsigned()->nullable();
            $table->string('zip')->nullable();
            $table->string('address')->nullable();
            $table->float('latitude', 10, 0)->nullable();
            $table->float('longitude', 10, 0)->nullable();
            $table->string('delivery_type', 45)->nullable();
            $table->integer('status')->default(1);
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
        Schema::dropIfExists('food_orders');
    }
}
