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
        Schema::create('realty_categories', function (Blueprint $table) {
            $table->id();
            $table->integer('parent_id')->unsigned()->nullable()->index();
            $table->foreign('parent_id', 'FK_parent_product_category')
                ->references('id')
                ->on('catalog_ad_categories')
                ->onUpdate('CASCADE')
                ->onDelete('SET NULL');
            $table->string('name')->nullable();
            $table->string('alias')->nullable()->unique();
            $table->integer('sort')->nullable();
            $table->string('h1')->nullable();
            $table->string('h2')->nullable();
            $table->text('text')->nullable();
            $table->text('title')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('color_id')->nullable()->constrained();
            $table->integer('active')->unsigned()->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('realty_categories');
    }
};
