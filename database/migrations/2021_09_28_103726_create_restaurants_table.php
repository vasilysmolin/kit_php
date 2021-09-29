<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestaurantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->string('title',255)->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('description',255)->nullable();
            $table->string('name',255)->nullable();
            $table->string('alias',255)->unique();
            $table->boolean('active')->default(0);
            $table->boolean('sort')->nullable();
            $table->string('street',255)->nullable();
            $table->string('house',255)->nullable();
            $table->string('coords',255)->nullable();
            $table->string('phone',255)->nullable();
            $table->string('email',255)->nullable();
            $table->timestamps();
            $table->foreign('user_id','FK_user_restaurant')
                ->references('id')
                ->on('users')
                ->onUpdate('RESTRICT')
                ->onDelete('RESTRICT');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('restaurants');
    }
}
