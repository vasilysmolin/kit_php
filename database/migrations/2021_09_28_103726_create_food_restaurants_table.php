<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoodRestaurantsTable extends Migration
{
    /**
     * Run the migrations..
     *
     * @return void
     */
    public function up()
    {
        Schema::create('food_restaurants', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->nullable();
            $table->unsignedBigInteger('profile_id');
            $table->string('description', 255)->nullable();
            $table->string('name', 255)->nullable();
            $table->json('work_time')->default(json_encode([30,60]));
            $table->json('delivery_time')->default(json_encode([600 ,1260]));
            $table->float('min_delivery_price', 8, 2)->default(0);
            $table->string('alias', 255)->unique();
            $table->boolean('active')->default(0);
            $table->boolean('sort')->nullable();
            $table->string('address', 255)->nullable();
            $table->float('latitude', 10, 0)->nullable();
            $table->float('longitude', 10, 0)->nullable();
            $table->string('house', 255)->nullable();
            $table->string('coords', 255)->nullable();
            $table->string('phone', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->boolean('isDelivery')->default(true)->after('active');
            $table->boolean('isPickup')->default(false)->after('active');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('profile_id', 'FK_profile_restaurant')
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
        Schema::dropIfExists('food_restaurants');
    }
}
