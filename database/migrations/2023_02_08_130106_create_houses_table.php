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
        Schema::create('houses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agent_id')->index()->nullable();
            $table->unsignedBigInteger('profile_id')->index()->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->string('state', 100)->default('new');
            $table->integer('sort')->nullable();
            $table->integer('total_floors')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('alias');
            $table->string('street')->nullable();
            $table->year('date_build')->nullable();
            $table->string('deadline')->nullable();
            $table->float('latitude', 10, 0)->nullable();
            $table->float('longitude', 10, 0)->nullable();
            $table->string('house')->nullable();
            $table->string('type')->nullable();
            $table->string('parking')->nullable();
            $table->float('ceiling_height', 8, 2)->nullable();
            $table->string('elite')->nullable();
            $table->string('finishing')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('realties', function (Blueprint $table) {
            $table->unsignedBigInteger('house_id')->index()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('houses');

        Schema::table('realties', function (Blueprint $table) {
            $table->dropColumn('house_id');
        });
    }
};
