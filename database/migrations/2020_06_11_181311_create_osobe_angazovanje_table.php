<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOsobeAngazovanjeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('osobe_angazovanje', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('osoba_id', 13)->unique();
            $table->text('reference')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();

            $table->foreign('osoba_id')->references('id')->on('tosoba')->onUpdate('cascade')->onDelete('restrict');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('osobe_angazovanje');
    }
}
