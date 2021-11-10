<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferencaLicencaZahtevTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referenca_licenca_zahtev', function (Blueprint $table) {
            $table->bigInteger('referenca_id');
            $table->bigInteger('licenca_zahtev_id');
            $table->timestamps();

            $table->unique(['referenca_id', 'licenca_zahtev_id'], 'referenca_licenca_zahtev_referenca_id_licenca_zahtev_id_unique');
            $table->foreign('referenca_id')->references('id')->on('treferenca')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('licenca_zahtev_id')->references('id')->on('tzahtev')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('referenca_licenca_zahtev');
    }
}
