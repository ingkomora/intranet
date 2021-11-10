<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePrijaveClanstvoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('prijave_clanstvo', function (Blueprint $table) {
            $table->integer('status_id')->nullable(false)->default(1);
            $table->string('broj_odluke_uo')->nullable();
            $table->date('datum_odluke_uo')->nullable();

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
        Schema::table('prijave_clanstvo', function (Blueprint $table) {
            $table->dropColumn('status_id');
            $table->dropColumn('broj_odluke_uo');
            $table->dropColumn('datum_odluke_uo');

            $table->dropForeign('statusi_status_id_foreign');


        });
    }
}
