<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZahteviTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zahtev_tip', function (Blueprint $table) {
            $table->id();
            $table->string('naziv');
            $table->text('napomena')->nullable();
        });
        Schema::create('zahtevi', function (Blueprint $table) {
            $table->id();
            $table->string('osoba_id',13);
            $table->string('zavodni_broj')->nullable();
            $table->date('datum_prijema')->nullable();
            $table->integer('zahtev_tip_id');
            $table->integer('status_id')->default(ZAHTEV_KREIRAN);
            $table->text('napomena')->nullable();
            $table->timestamps();

            $table->foreign('osoba_id')->references('id')->on('tosoba')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('status_id')->references('id')->on('statusi')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('zahtev_tip_id')->references('id')->on('zahtev_tip')->onUpdate('cascade')->onDelete('restrict');



        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zahtevi');
        Schema::dropIfExists('zahtev_tip');
    }
}
