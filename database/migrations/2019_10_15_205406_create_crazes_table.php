<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCrazesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crazes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->unsignedBigInteger('trend');
            $table->foreign('trend')
            ->references('id')->on('trends')
            ->onDelete('cascade');
            $table->unsignedBigInteger('location');
            $table->foreign('location')
            ->references('id')->on('locations')
            ->onDelete('cascade');
            $table->dateTimeTz('craze_created_at');
            $table->dateTimeTz('craze_as_of');
            $table->bigInteger('tweet_volume');
            $table->boolean('promoted_content');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('crazes');
    }
}
