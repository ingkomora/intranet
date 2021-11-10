<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SyncTzahtevWithSiPrijavaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tzahtev', function (Blueprint $table) {
            $table->integer('vrsta_posla_id');
            $table->integer('app_korisnik_id')->nullable();
            $table->string('zavodni_broj')->nullable();
            $table->string('barcode')->nullable();
            $table->string('reg_oblast_id');
            $table->integer('reg_pod_oblast_id');
            $table->text('napomena')->nullable();
            $table->bigInteger('si_prijava_id')->nullable();


            $table->foreign('prijava_clan_id')->references('id')->on('prijave_clanstvo')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('reg_oblast_id')->references('id')->on('treg_oblast')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('reg_pod_oblast_id')->references('id')->on('treg_podoblast')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('app_korisnik_id')->references('id')->on('tappkorisnik')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('')->references('id')->on('vrste_poslova')->onUpdate('cascade')->onDelete('restrict');
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
        Schema::table('tzahtev', function (Blueprint $table) {
            $table->dropColumn('reg_oblast_id');
            $table->dropColumn('reg_pod_oblast_id');
            $table->dropColumn('app_korisnik_id');
            $table->dropColumn('vrsta_posla_id');
            $table->dropForeign('reg_oblast_id');
            $table->dropForeign('reg_pod_oblast_id');
            $table->dropForeign('app_korisnik_id');
            $table->dropForeign('vrsta_posla_id');
            $table->dropForeign('si_prijava_id');
        });
    }
}
