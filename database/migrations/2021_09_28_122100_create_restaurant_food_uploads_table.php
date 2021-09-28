<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestaurantFoodUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurant_food_uploads', function (Blueprint $table) {
            $table->unsignedBigInteger('restaurant_food_id');
            $table->unsignedBigInteger('upload_id');
            $table->foreign('restaurant_food_id', 'FK_restaurant_food_uploads_restaurant_food_id')
                ->references('id')
                ->on('restaurant_foods');
            $table->foreign('upload_id', 'FK_restaurant_food_uploads_upload_id')
                ->references('id')
                ->on('upload_files');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('restaurant_food_uploads');
    }
}
