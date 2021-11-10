<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePodOblastVrstaPoslaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pod_oblast_vrsta_posla', function (Blueprint $table) {
            $table->bigInteger('pod_oblast_id');
            $table->bigInteger('vrsta_posla_id');
            $table->timestamps();

            $table->foreign('pod_oblast_id')->references('id')->on('treg_podoblast')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('vrsta_posla_id')->references('id')->on('vrste_poslova')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pod_oblast_vrsta_posla');
    }
}
