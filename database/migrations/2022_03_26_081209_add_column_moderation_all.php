<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnModerationAll extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('reason', 100)->nullable();
        });

        Schema::table('jobs_vacancies', function (Blueprint $table) {
            $table->string('reason', 100)->nullable();
        });

        Schema::table('jobs_resumes', function (Blueprint $table) {
            $table->string('reason', 100)->nullable();
        });

        Schema::table('services', function (Blueprint $table) {
            $table->string('reason', 100)->nullable();
        });

        Schema::table('catalog_ads', function (Blueprint $table) {
            $table->string('reason', 100)->nullable();
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('reason');
        });

        Schema::table('jobs_vacancies', function (Blueprint $table) {
            $table->dropColumn('reason');
        });

        Schema::table('jobs_resumes', function (Blueprint $table) {
            $table->dropColumn('reason');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('reason');
        });

        Schema::table('catalog_ads', function (Blueprint $table) {
            $table->dropColumn('reason');
        });
    }
}
