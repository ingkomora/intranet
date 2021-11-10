<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDelovodniciOrganizacioneJediniceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delovodnici_organizacione_jedinice', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('oznaka');
            $table->string('naziv');
            $table->integer('parent_id')->nullable();
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
        Schema::dropIfExists('delovodnici_organizacione_jedinice');
    }
}
