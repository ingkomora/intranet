<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTreferencaTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('treferenca', function (Blueprint $table) {
            $table->string('odgovorno_lice_licenca_id',11)->nullable();
            $table->timestamps();

            $table->foreign('odgovorno_lice_licenca_id')->references('id')->on('tlicenca')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('treferenca', function (Blueprint $table) {
            $table->dropForeign('treferenca_odgovorno_lice_licenca_id_foreign');
            $table->dropColumn('odgovorno_lice_licenca_id');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');

        });
    }
}
