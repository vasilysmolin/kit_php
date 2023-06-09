<?php

use App\Objects\FeedType\FeedType;
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
        Schema::create('feeds', function (Blueprint $table) {
            $table->id();
            $table->string('url', 255);
            $table->string('type', 255);
            $table->enum('name', FeedType::keys());
            $table->unsignedBigInteger('profile_id');
            $table->foreign('profile_id', 'FK_profile_resume')
                ->references('id')
                ->on('profiles')
                ->onUpdate('RESTRICT')
                ->onDelete('CASCADE');
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
        Schema::dropIfExists('feeds');
    }
};
