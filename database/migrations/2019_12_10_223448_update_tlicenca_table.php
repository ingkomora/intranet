<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTlicencaTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('tlicenca', function (Blueprint $table) {
            $table->string('id', 11)->change();
            $table->integer('prijava_id')->nullable();
            $table->string('broj_resenja')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('tlicenca', function (Blueprint $table) {
            $table->string('id', 9)->change();
            $table->dropColumn('prijava_id');
            $table->dropColumn('broj_resenja');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
    }
}
