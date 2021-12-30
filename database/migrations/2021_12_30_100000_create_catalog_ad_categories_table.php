<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCatalogAdCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog_ad_categories', function (Blueprint $table) {
            $table->id();
            $table->integer('parent_id')->unsigned()->nullable()->index();
            $table->unsignedBigInteger('meta_id')->nullable();
            $table->string('name')->nullable();
            $table->string('alias')->nullable()->unique();
            $table->integer('sort')->nullable();
            $table->string('h1')->nullable();
            $table->string('h2')->nullable();
            $table->text('text')->nullable();
            $table->text('title')->nullable();
            $table->text('description')->nullable();
            $table->integer('active')->unsigned()->default(1);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('meta_id', 'FK_catalog_meta_id')
                ->references('id')
                ->on('catalog_metas')
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
        Schema::dropIfExists('catalog_ad_categories');
    }
}
