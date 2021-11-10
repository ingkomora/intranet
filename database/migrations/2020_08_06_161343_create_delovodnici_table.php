<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDelovodniciTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delovodnici', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('osnovni_broj');
            $table->boolean('prepis')->default(0);
            $table->string('predmet');
            $table->integer('pod_broj')->nullable();
            $table->date('datum_prijema')->nullable();
            $table->string('posiljalac')->nullable();
            $table->date('posiljalac_datum')->nullable();
            $table->integer('organizaciona_jedinica_id')->nullable();
            $table->integer('brojac')->default(0);
            $table->integer('status_id')->nullable();
            $table->timestamps();

            $table->foreign('organizaciona_jedinica_id')->references('id')->on('delovodnik_organizacione_jedinice')->onUpdate('cascade')->onDelete('restrict');
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
        Schema::dropIfExists('delovodnici');
    }
}
