<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferencaSiPrijavaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referenca_si_prijava', function (Blueprint $table) {
            $table->bigInteger('referenca_id');
            $table->bigInteger('si_prijava_id');
            $table->timestamps();

            $table->foreign('referenca_id')->references('id')->on('treferenca')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('si_prijava_id')->references('id')->on('si_prijava')->onUpdate('cascade')->onDelete('restrict');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('referenca_si_prijava');
    }
}
