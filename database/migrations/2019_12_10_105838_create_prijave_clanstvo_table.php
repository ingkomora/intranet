<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrijaveClanstvoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prijave_clanstvo', function (Blueprint $table) {
            $table->increments('id');
            $table->string('osoba_id', 13)->nullable(false);
            $table->date('datum_prijema')->nullable(true);
            $table->integer('app_korisnik_id')->nullable(true);
            $table->string('zavodni_broj')->nullable(true);
            $table->string('barcode')->nullable(true);
            $table->text('napomena')->nullable(true);
            $table->timestamps();

            $table->foreign('osoba_id')->references('id')->on('tosoba')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('app_korisnik_id')->references('id')->on('tappkorisnik')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prijave_clanstvo');
    }
}
