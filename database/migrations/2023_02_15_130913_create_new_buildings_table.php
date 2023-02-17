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
        Schema::create('new_buildings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('profile_id')->index();
            $table->string('external_id')->nullable()->index();
            $table->unsignedBigInteger('category_id')->nullable()->index();
            $table->unsignedBigInteger('house_id')->nullable()->index();
            $table->string('state', 100)->default('new');
            $table->string('reason', 100)->nullable();
            $table->string('article')->nullable();
            $table->integer('sort')->nullable();
            $table->string('name')->nullable();
            $table->string('title', 1000)->nullable();
            $table->text('description')->nullable();
            $table->string('alias')->nullable()->unique();
            $table->integer('active')->unsigned()->default(1);
            $table->float('price', 10, 0)->nullable();
            $table->float('sale_price', 10, 0)->nullable();
            $table->string('price_per_square')->nullable();
            $table->string('video')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('sub_agents', function (Blueprint $table) {
            $table->unsignedBigInteger('profile_id')->index()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('new_buildings');
        Schema::table('sub_agents', function (Blueprint $table) {
            $table->dropColumn('profile_id');
        });
    }
};
