<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnTimezoneFromCities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->unsignedInteger('postal_code')->after('delivery_threshold')->nullable();
            $table->string('kladr_id')->after('postal_code')->nullable();
            $table->string('fias_id')->after('postal_code')->nullable();
            $table->string('population')->after('fias_id')->nullable();
            $table->foreignId('country_id')
                ->nullable()
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('timezone_id')
                ->nullable()
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropConstrainedForeignId('timezone_id');
            $table->dropConstrainedForeignId('country_id');
        });
    }
}
