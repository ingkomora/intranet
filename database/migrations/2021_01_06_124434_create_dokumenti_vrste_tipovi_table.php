<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDokumentiVrsteTipoviTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dokumenti_vrste_tipovi', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('naziv');
            $table->text('napomena')->comment('kakvi su tipovi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dokumenti_vrste_tipovi');
    }
}
