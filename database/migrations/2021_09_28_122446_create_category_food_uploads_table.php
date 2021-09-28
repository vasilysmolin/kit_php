<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryFoodUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_food_uploads', function (Blueprint $table) {
            $table->unsignedBigInteger('category_food_id');
            $table->unsignedBigInteger('upload_id');
            $table->foreign('restaurant_food_id', 'FK_category_food_uploads_category_food_id')
                ->references('id')
                ->on('category_food');
            $table->foreign('upload_id', 'FK_category_food_uploads_upload_id')
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
        Schema::dropIfExists('category_food_uploads');
    }
}
