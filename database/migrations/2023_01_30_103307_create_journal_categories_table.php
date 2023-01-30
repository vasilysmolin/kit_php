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
        Schema::create('journal_categories', function (Blueprint $table) {
            $table->id();
            $table->integer('parent_id')->unsigned()->nullable()->index();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('slug')->unique();
            $table->integer('sort')->nullable();
            $table->integer('color_id')->nullable();
            $table->integer('active')->unsigned()->default(1);
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
        Schema::dropIfExists('journal_categories');
    }
};
