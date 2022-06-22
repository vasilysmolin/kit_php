<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnModerationStateAll extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('state', 100)->after('email')->default('step');
        });
        Schema::table('jobs_vacancy_categories', function (Blueprint $table) {
            $table->string('state', 100)->after('name')->default('new');
        });

        Schema::table('jobs_resumes_categories', function (Blueprint $table) {
            $table->string('state', 100)->after('name')->default('new');
        });

        Schema::table('jobs_vacancies', function (Blueprint $table) {
            $table->string('state', 100)->after('name')->default('new');
        });

        Schema::table('jobs_resumes', function (Blueprint $table) {
            $table->string('state', 100)->after('name')->default('new');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->string('state', 100)->after('name')->default('new');
        });

        Schema::table('catalog_ads', function (Blueprint $table) {
            $table->string('state', 100)->after('name')->default('new');
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
            $table->dropColumn('state');
        });
        Schema::table('jobs_vacancy_categories', function (Blueprint $table) {
            $table->dropColumn('state');
        });

        Schema::table('jobs_resumes_categories', function (Blueprint $table) {
            $table->dropColumn('state');
        });

        Schema::table('jobs_vacancies', function (Blueprint $table) {
            $table->dropColumn('state');
        });

        Schema::table('jobs_resumes', function (Blueprint $table) {
            $table->dropColumn('state');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('state');
        });

        Schema::table('catalog_ads', function (Blueprint $table) {
            $table->dropColumn('state');
        });
    }
}
