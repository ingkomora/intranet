<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDokumentiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dokumenti', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('naziv')->comment('=title meta dms');
            $table->bigInteger('dokument_vrsta_id');
            $table->date('datum')->comment('datum prijema ili datum resenja (isti kao i zavodjenja)')->nullable();
            $table->string('zavodni_broj')->comment('zavodni broj')->nullable();
            $table->string('broj')->comment('opcioni broj - broj resenja ako nije isti kao zavodni')->nullable();
            $table->integer('status_id');
            $table->string('url')->comment('naziv i putanja do datoteke')->nullable();
            $table->text('napomena')->comment('o dokumentu')->nullable();
            $table->string('author')->comment('meta dms')->nullable();
            $table->string('subject')->comment('meta dms')->nullable();
            $table->string('keywords')->comment('meta dms')->nullable();
            $table->string('category')->comment('meta dms')->nullable();
            $table->string('comments')->comment('meta dms')->nullable();

            $table->morphs('requestable'); // id zahteva iz tsi_prijava i tzahtev i zahtevi
            $table->timestamps();

            $table->foreign('dokument_vrsta_id')->references('id')->on('dokumenti_vrste')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('status_id')->references('id')->on('statusi')->onUpdate('cascade')->onDelete('restrict');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dokumenti');
    }
}
