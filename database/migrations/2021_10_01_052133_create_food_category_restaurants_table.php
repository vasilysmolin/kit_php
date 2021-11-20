<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoodCategoryRestaurantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('food_category_restaurants', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->nullable();
            $table->string('alias', 255)->unique();
            $table->string('description', 255)->nullable();
            $table->integer('sort')->nullable()->default(1);
            $table->boolean('active')->default(1);
            $table->boolean('isModeration')->default(0);
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
        Schema::dropIfExists('category_restaurants');
    }
}
