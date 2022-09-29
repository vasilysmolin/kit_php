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
        Schema::create('realty_filters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('name')->nullable();
            $table->string('type')->default('select');
            $table->string('alias')->nullable()->unique();
            $table->integer('sort')->unsigned()->nullable();
            $table->integer('active')->nullable()->default(1);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('category_id', 'FK_realty_filters_category_id')
                ->references('id')
                ->on('realty_categories')
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
        Schema::dropIfExists('realty_filters');
    }
};
