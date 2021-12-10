<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobsCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('alias', 255)->unique();
            $table->string('description', 255)->nullable();
            $table->integer('parent_id')->nullable();
            $table->integer('sort')->nullable()->default(1);
            $table->boolean('active')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jobs_categories');
    }
}
