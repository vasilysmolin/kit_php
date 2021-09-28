<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestaurantUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurant_uploads', function (Blueprint $table) {
            $table->unsignedBigInteger('restaurant_id');
            $table->unsignedBigInteger('upload_id');
            $table->foreign('restaurant_id', 'FK_restaurant_uploads_restaurant_id')
                ->references('id')
                ->on('restaurants');
            $table->foreign('upload_id', 'FK_restaurant_uploads_upload_id')
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
        Schema::dropIfExists('restaurant_uploads');
    }
}
