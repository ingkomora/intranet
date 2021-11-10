<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTlicencatipTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tlicencatip', function (Blueprint $table) {
            $table->string('oznaka', 15)->nullable();
            $table->string('opis')->nullable();
            $table->integer('generacija')->comment('Tiplovi licenci od 2003 => 1, od 2019 => 2, od 2021 => 3');
            $table->timestamps();
            $table->bigInteger('profesionalni_naziv_id')->nullable();

            $table->foreign('profesionalni_naziv_id')->references('id')->on('profesionalni_nazivi')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tlicencatip', function (Blueprint $table) {
            $table->dropForeign('profesionalni_naziv_id');

            $table->dropColumn('oznaka');
            $table->dropColumn('opis');
            $table->dropColumn('generacija');
            $table->dropColumn('profesionalni_naziv_id');
        });
    }
}
