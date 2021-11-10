<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZahteviTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zahtevi', function (Blueprint $table) {

            $table->increments('id');
            $table->string('osoba_id', 13);
            $table->integer('vrsta_posla_id');
            $table->string('reg_oblast_id'); // imamo preko podoblast_id ali mora da ostane jer je deo slozenog kljuca
            $table->integer('reg_pod_oblast_id');
            $table->integer('status_id')->default(PRIJAVA_KREIRANA);
            $table->date('datum_prijema')->nullable();
            $table->integer('app_korisnik_id')->nullable();
            $table->string('zavodni_broj')->nullable();
            $table->string('barcode')->nullable();
            $table->text('napomena')->nullable();
            $table->timestamps();

            $table->unique(['osoba_id', 'vrsta_posla_id', 'reg_oblast_id', 'reg_pod_oblast_id'], 'zahtevi_osoba_vrsta_posla_id_oblast_id_podoblast_id_unique');
            $table->foreign('osoba_id')->references('id')->on('tosoba')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('status_id')->references('id')->on('statusi')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('vrsta_posla_id')->references('id')->on('vrste_poslova')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('reg_oblast_id')->references('id')->on('treg_oblast')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('reg_pod_oblast_id')->references('id')->on('treg_podoblast')->onUpdate('cascade')->onDelete('restrict');
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
    }
}
