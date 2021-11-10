<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFirmeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('firme', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('mb',8)->unique();
            $table->string('pib', 9)->unique();
            $table->string('naziv');
            $table->string('drzava');
            $table->string('mesto');
            $table->integer('pb')->nullable();
            $table->string('adresa');
            $table->integer('opstina_id');
            $table->string('fax')->nullable();
            $table->string('telefon')->nullable();
            $table->string('email')->nullable();
            $table->string('web')->nullable();
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
        Schema::dropIfExists('firme');
    }
}
