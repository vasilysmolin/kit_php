<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('realties', function (Blueprint $table) {
            $table->year('date_build')->nullable();
            $table->date('cadastral_number')->nullable();
            $table->date('ceiling_height')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('realties', function (Blueprint $table) {
            $table->dropColumn('date_build');
            $table->dropColumn('cadastral_number');
            $table->dropColumn('ceiling_height');
        });
    }
};
