<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOsiguranjeOsobaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('osiguranje_osoba', function (Blueprint $table) {
            $table->bigInteger('osiguranja_id');
            $table->string('osoba_id',13);
            $table->date('datum_provere')->nullable();
            $table->timestamps();

            $table->foreign('osiguranja_id')->references('id')->on('osiguranja')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('osoba_id')->references('id')->on('tosoba')->onUpdate('cascade')->onDelete('restrict');
            $table->unique(['osiguranja_id','osoba_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('osiguranje_osoba');
    }
}
