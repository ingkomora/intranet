<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTsiPrijavaTableUniqueIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('si_prijava', function (Blueprint $table) {
            //naknadno rucno dodata polja u tabeli
            $table->bigInteger('vrsta_posla_id');
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
        Schema::table('si_prijava', function (Blueprint $table) {
            $table->dropForeign('tsi_prijava_vrste_poslova_id_fk');
            $table->dropColumn('vrsta_posla_id');
        });
    }
}
