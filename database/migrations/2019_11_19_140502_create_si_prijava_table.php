<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiPrijavaTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('si_prijava', function (Blueprint $table) {
            $table->increments('id');
            $table->string('osoba_id', 13)->nullable(false);
            $table->string('reg_oblast_id')->nullable(false);
            $table->integer('reg_pod_oblast_id')->nullable(false);
            $table->integer('zvanje_id')->nullable(false);
            $table->integer('si_vrsta_id')->nullable(false);
            $table->integer('status_prijave')->nullable(false)->default(2);
            $table->date('datum_prijema')->nullable(true);
            $table->integer('app_korisnik_id')->nullable(true);
            $table->string('zavodni_broj')->nullable(true);
            $table->smallInteger('strucni_rad')->nullable(true);
            $table->text('tema')->nullable(true);
            $table->string('barcode')->nullable(true);
            $table->timestamps();
            $table->integer('vrsta_posla_id');
            $table->integer('uspeh_id');

            $table->foreign('osoba_id')->references('id')->on('tosoba')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('zvanje_id')->references('id')->on('tzvanje')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('reg_oblast_id')->references('id')->on('treg_oblast')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('reg_pod_oblast_id')->references('id')->on('treg_podoblast')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('si_vrsta_id')->references('id')->on('tvrstasi')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('app_korisnik_id')->references('id')->on('tappkorisnik')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('vrsta_posla_id')->references('id')->on('vrste_poslova')->onUpdate('cascade')->onDelete('restrict');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('si_prijava');
    }
}
