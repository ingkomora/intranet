<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatusiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statusi', function (Blueprint $table) {
            $table->increments('id');
            $table->string('naziv');
            $table->string('const');
            $table->integer('log_status_grupa_id');
            $table->text('napomena');

            $table->foreign('log_status_grupa_id')->references('id')->on('logovi_statusi_grupe')->onUpdate('cascade')->onDelete('restrict');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('statusi');
    }
}
