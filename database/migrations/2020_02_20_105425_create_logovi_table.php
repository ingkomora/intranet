<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogoviTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logovi', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('naziv');
            $table->integer('log_status_grupa_id');
            $table->text('napomena')->nullable();
            $table->morphs('loggable');
            $table->timestamps();

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
            Schema::dropIfExists('logovi');
    }
}
