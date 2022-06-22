<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToAdsLocations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('catalog_ads', function (Blueprint $table) {
            $table->string('street')->nullable();
            $table->string('house')->nullable();
            $table->float('latitude', 10, 0)->nullable();
            $table->float('longitude', 10, 0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('catalog_ads', function (Blueprint $table) {
            $table->dropColumn('street');
            $table->dropColumn('house');
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
        });
    }
}
