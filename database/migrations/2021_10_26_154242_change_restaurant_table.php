<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeRestaurantTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->renameColumn('street', 'address');
            $table->json('work_time')->after('name')->default(json_encode([30,60]));
            $table->json('delivery_time')->after('name')->default(json_encode([600 ,1260]));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->renameColumn('address','street');
            $table->dropColumn('work_time');
            $table->dropColumn('delivery_time');
        });
    }
}
