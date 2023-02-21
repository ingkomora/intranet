<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRokUspehPolaganjeToSiPrijavaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('si_prijava', function (Blueprint $table) {
            $table->foreign('uspeh_id')->references('id')->on('si_uspesi')->onUpdate('cascade')->onDelete('restrict');
            $table->string('rok')->nullable();
            $table->date('datum_polaganja')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function($table) {
            $table->dropColumn('uspeh_id');
            $table->dropColumn('rok');
            $table->dropColumn('datum_polaganja');
        });
    }
}
