<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->string('naziv');
            $table->dateTime('pocetak');
            $table->dateTime('kraj');
            $table->integer('events_grupe_id');
            $table->integer('status_id');
            $table->timestamps();

            $table->foreign('events_grupe_id')->references('id')->on('events_grupe')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('status_id')->references('id')->on('statusi')->onUpdate('cascade')->onDelete('restrict');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');

    }
}
