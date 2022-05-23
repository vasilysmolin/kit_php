<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnFullNameRegions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('regions', function (Blueprint $table) {
            $table->unsignedInteger('postal_code')->after('alias')->nullable();
            $table->string('full_name')->after('alias')->nullable();
            $table->string('kladr_id')->after('alias')->nullable();
            $table->string('fias_id')->after('alias')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('regions', function (Blueprint $table) {
            $table->dropColumn('postal_code');
            $table->dropColumn('full_name');
            $table->dropColumn('fias_id');
            $table->dropColumn('kladr_id');
        });
    }
}
