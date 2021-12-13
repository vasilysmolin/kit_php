<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobsResumeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs_resume', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('profile_id');
            $table->string('title', 255)->nullable();
            $table->string('description', 255)->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('address', 255)->nullable();
            $table->string('phone', 255)->nullable();
            $table->string('name', 255)->nullable();
            $table->string('duties')->nullable();
            $table->string('demands')->nullable();
            $table->string('additionally')->nullable();
            $table->string('education')->nullable();
            $table->string('experience')->nullable();
            $table->string('schedule')->nullable();
            $table->string('alias', 255)->unique();
            $table->boolean('active')->default(0);
            $table->boolean('sort')->nullable();
            $table->float('latitude', 10, 0)->nullable();
            $table->float('longitude', 10, 0)->nullable();
            $table->integer('work_experience')->default(0);
            $table->integer('price')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('category_id', 'FK_jobs_vacancy_category_id')
                ->references('id')
                ->on('jobs_resume_categories')
                ->onUpdate('RESTRICT')
                ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jobs_vacancies');
    }
}
