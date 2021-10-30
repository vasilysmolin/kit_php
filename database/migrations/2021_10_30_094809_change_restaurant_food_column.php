<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeRestaurantFoodColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('restaurant_foods', function (Blueprint $table) {
            $table->dropForeign('FK_category_id_food');
            $table->dropColumn('category_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('restaurant_foods', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id', 'FK_category_id_food')
                ->references('id')
                ->on('category_food')
                ->onUpdate('RESTRICT')
                ->onDelete('RESTRICT');
        });
    }
}
