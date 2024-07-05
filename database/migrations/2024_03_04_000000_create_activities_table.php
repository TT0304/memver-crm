<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable();
            $table->string('type');
            $table->text('comment')->nullable();
            $table->json('additional')->nullable();
            $table->datetime('schedule_from')->nullable();
            $table->datetime('schedule_to')->nullable();
            $table->boolean('is_done')->default(0);
            $table->string('location')->nullable();

            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('client_users')->onDelete('cascade');

            $table->bigInteger('client_id')->unsigned()->nullable(); 
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
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
        Schema::dropIfExists('activities');
    }
}
