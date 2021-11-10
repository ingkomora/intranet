<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegOblastVrstaPoslaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reg_oblast_vrsta_posla', function (Blueprint $table) {
            $table->char('reg_oblast_id',1);
            $table->bigInteger('vrsta_posla_id');
            $table->timestamps();

            $table->foreign('reg_oblast_id')->references('id')->on('treg_oblast')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('vrsta_posla_id')->references('id')->on('vrste_poslova')->onUpdate('cascade')->onDelete('restrict');

        });

        /*Artisan::call('db:seed', [
            '--class' => RegOblastVrstaPoslaSeeder::class,
        ]);*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reg_oblast_vrsta_posla');
    }
}
