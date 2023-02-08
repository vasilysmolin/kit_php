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
            $table->string('name');
            $table->string('street')->nullable();
            $table->date('date')->nullable();
            $table->string('deadline')->nullable();
            $table->string('house')->nullable();
            $table->string('type')->nullable();
            $table->string('parking')->nullable();
            $table->float('сeiling_height', 8, 2)->nullable();
            $table->string('elite')->nullable();
            $table->string('finishing')->nullable();
            $table->timestamps();
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
    }
};
