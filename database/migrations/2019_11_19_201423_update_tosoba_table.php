<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTosobaTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('tosoba', function (Blueprint $table) {
            $table->date('datumrodjenja')->nullable();
            $table->string('prebivalistedrzava')->nullable();
            $table->text('vrsta_poslova')->nullable();
            $table->float('godine_radnog_iskustva')->nullable();
            $table->smallInteger('bolonja')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        //
        Schema::table('tosoba', function (Blueprint $table) {
            $table->dropColumn('datumrodjenja');
            $table->dropColumn('prebivalistedrzava');
            $table->dropColumn('vrsta_poslova');
            $table->dropColumn('godine_radnog_iskustva');
            $table->dropColumn('bolonja');
            $table->dropColumn('timestamps');
        });
    }
}
