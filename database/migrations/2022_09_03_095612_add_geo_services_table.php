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
        Schema::table('jobs_resumes', function (Blueprint $table) {
            $table->string('street')->nullable();
            $table->string('house')->nullable();
        });
        Schema::table('jobs_vacancies', function (Blueprint $table) {
            $table->string('street')->nullable();
            $table->string('house')->nullable();
        });
        Schema::table('services', function (Blueprint $table) {
            $table->string('street')->nullable();
            $table->string('house')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jobs_resumes', function (Blueprint $table) {
            $table->dropColumn('street');
            $table->dropColumn('house');
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
        });
        Schema::table('jobs_vacancies', function (Blueprint $table) {
            $table->dropColumn('street');
            $table->dropColumn('house');
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
        });
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('street');
            $table->dropColumn('house');
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
        });
    }
};
