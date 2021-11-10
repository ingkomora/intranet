<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTzahtevTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('tzahtev', function (Blueprint $table) {
            $table->integer('prijava_clan_id')->unsigned()->index()->nullable();
            $table->string('licenca_broj', 11)->nullable();
            $table->string('licenca_broj_resenja')->nullable();
            $table->date('licenca_datum_resenja')->nullable();
            $table->timestamps();


            $table->foreign('prijava_clan_id')->references('id')->on('prijave_clanstvo')->onUpdate('cascade')->onDelete('restrict');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('tzahtev', function (Blueprint $table) {
            $table->dropColumn('prijava_clan_id');
            $table->dropColumn('licenca_broj');
            $table->dropColumn('licenca_broj_resenja');
            $table->dropColumn('licenca_datum_resenja');
            $table->dropTimestamps();

            $table->dropForeign('zahtev_prijava_clan_id_foreign');
        });
    }
}
