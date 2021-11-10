<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMirovanjaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mirovanja', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('osoba_id', 13);
            $table->date('datum_pocetka');
            $table->string('broj_resenja_pocetka')
                ->nullable()
                ->unique()
                ->default(null);
            $table->date('datum_isteka')->nullable();
            $table->string('broj_resenja_isteka')
                ->nullable()
                ->unique()
                ->default(null);
            $table->date('datum_prijema')->nullable();
            $table->string('zavodni_broj')
                ->nullable()
                ->unique()
                ->default(null);
            $table->boolean('aktivno')->default(false);
            $table->bigInteger('status_id');
            $table->bigInteger('mirovanje_razlog_id');
            $table->string('drugi_razlog')
                ->comment('Ukoliko je član izabrao opciju "drugi opravdani razlog"')
                ->nullable();
            $table->string('drugi_razlog_padez')
                ->comment('Na primer: ...zbog nekog ličnog razloga')
                ->nullable();
            $table->text('dokumentacija')
                ->comment('Priložena dokumentacija uz zahtev za mirovanje');
            $table->string('broj_sednice')
                ->nullable()
                ->comment('ukoliko je organ odobrio zahtev, ovde se upisuje broj sednice u odgovarajućem padežu (zavisi od šablona prema kome se generiše rešenje. Na primer: petoj redovnoj ili drugoj vanrednoj');
            $table->text('napomena')->nullable();
            $table->timestamps();

            /*
             *  CONSTRAINTS
             */
            $table->foreign('osoba_id')
                ->references('id')
                ->on('tosoba')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('mirovanje_razlog_id')
                ->references('id')
                ->on('mirovanja_razlozi')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('status_id')
                ->references('id')
                ->on('statusi')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->unique(['osoba_id', 'datum_pocetka']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mirovanja');
    }
}
