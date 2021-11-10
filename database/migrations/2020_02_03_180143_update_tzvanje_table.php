<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTzvanjeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tzvanje', function (Blueprint $table) {
            $table->char('reg_oblast_id')->nullable();
            $table->foreign('reg_oblast_id')->references('id')->on('treg_oblast')->onUpdate('cascade')->onDelete('restrict');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tzvanje', function (Blueprint $table) {
            $table->dropColumn('reg_oblast_id');
            $table->dropForeign('tzvanje_reg_oblast_id_foreign');

        });
    }
}
