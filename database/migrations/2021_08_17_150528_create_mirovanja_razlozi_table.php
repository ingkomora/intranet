<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMirovanjaRazloziTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mirovanja_razlozi', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('naziv');
            $table->string('naziv_full');
            $table->string('naziv_padez')->comment('U kolonu je upisan naziv_full u odgovarajućem padežu, koristi se prilikom generisanja rešenja.');
            $table->integer('trajanje')->comment('Maksimalno trajanje mirovanja u mesecima')->nullable();
            $table->boolean('visible')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mirovanja_razlozi');
    }
}
