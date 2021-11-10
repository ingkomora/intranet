<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTprijavapromenapodatakaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tprijavapromenapodataka', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->string('ime', 50);
            $table->string('prezime', 50);
            $table->string('brlic', 11);
            $table->string('adresa', 150)->nullable();
            $table->string('mesto', 50)->nullable();
            $table->string('pbroj', 50)->nullable();
            $table->integer('topstina_id')->nullable();
            $table->string('tel', 20)->nullable();
            $table->string('mob', 20)->nullable();
            $table->string('email', 50)->nullable();
            $table->string('nazivfirm')->nullable();
            $table->string('mestofirm', 50)->nullable();
            $table->string('opstinafirm', 50)->nullable();
            $table->string('emailfirm', 50)->nullable();
            $table->string('telfirm', 20)->nullable();
            $table->string('wwwfirm', 150)->nullable();
            $table->string('ipaddress', 50)->nullable();
            $table->timestamp('datumprijema', 6)->nullable();
            $table->timestamp('datumobrade', 6)->nullable();
            $table->integer('obradjen')->default(0)->nullable();
            $table->string('mbfirm', 8)->nullable();
            $table->string('pibfirm', 9)->nullable();
            $table->string('adresafirm')->nullable();
            $table->string('napomena')->nullable();
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
        Schema::dropIfExists('tprijavapromenapodataka');
    }
}
