<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationColorsToCategoryAds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('catalog_ad_categories', function (Blueprint $table) {
            $table->foreignId('color_id')->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('catalog_ad_categories', function (Blueprint $table) {
            $table->dropForeign('catalog_ad_categories_color_id_foreign');
            $table->dropColumn('color_id');
        });
    }
}
