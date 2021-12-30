<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('profile_id');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->string('title', 255)->nullable();
            $table->string('description', 255)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('name', 255)->nullable();
            $table->string('alias', 255)->unique();
            $table->boolean('active')->default(1);
            $table->boolean('contract')->default(0);
            $table->boolean('guarantee')->default(0);
            $table->boolean('hourly_payment')->default(0);
            $table->boolean('consultation')->default(0);
            $table->boolean('sort')->nullable();
            $table->float('latitude', 10, 0)->nullable();
            $table->float('longitude', 10, 0)->nullable();
            $table->string('phone', 255)->nullable();
            $table->integer('work_experience')->default(0);
            $table->integer('price')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('category_id', 'FK_jobs_services_category_id')
                ->references('id')
                ->on('service_categories')
                ->onUpdate('RESTRICT')
                ->onDelete('CASCADE');
            $table->foreign('city_id', 'FK_city_service')
                ->references('id')
                ->on('cities')
                ->onUpdate('RESTRICT')
                ->onDelete('CASCADE');
            $table->foreign('profile_id', 'FK_profile_service')
                ->references('id')
                ->on('profiles')
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
        Schema::dropIfExists('services');
    }
}
