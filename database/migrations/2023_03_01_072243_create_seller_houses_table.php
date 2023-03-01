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
        Schema::create('seller_houses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('profile_id')->index()->nullable();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('alias')->nullable();
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
        Schema::dropIfExists('seller_houses');
    }
};
