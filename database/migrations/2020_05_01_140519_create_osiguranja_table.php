<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOsiguranjaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('osiguranja', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('osiguranje_vrsta');
            $table->integer('osiguranje_tip_id');
            $table->string('osiguravajuca_kuca_mb',8); //osiguravajuca_kuca
            $table->string('ugovarac_osiguranja_mb',8); //ugovarac osiguranja
            $table->string('polisa_broj');
            $table->text('polisa_predmet'); //opis polise
            $table->bigInteger('polisa_pokrice_id');
            $table->string('polisa_iskljucenost')->nullable();
            $table->string('polisa_teritorijalni_limit')->nullable();
            $table->date('polisa_datum_izdavanja')->nullable();
            $table->date('polisa_datum_pocetka');
            $table->date('polisa_datum_zavrsetka');
            $table->integer('status_polise_id');
            $table->integer('status_dokumenta_id');
            $table->text('napomena')->nullable();
            $table->timestamps();

            $table->foreign('osiguranje_tip_id')->references('id')->on('osiguranje_tip')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('osiguravajuca_kuca_mb')->references('mb')->on('firme')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('ugovarac_osiguranja_mb')->references('mb')->on('firme')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('polisa_pokrice_id')->references('id')->on('osiguranja_polise_pokrica')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('status_polise_id')->references('id')->on('statusi')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('status_dokumenta_id')->references('id')->on('statusi')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('osiguranja');
    }
}
