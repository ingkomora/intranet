<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('document_type_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->string('registry_number')
                ->nullable()
                ->comment('zavodni broj');

            $table->string('registry_date')
                ->nullable()
                ->comment('datum zavodjenja');

            $table->foreignId('registry_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreignId('status_id')
                ->constrained('statusi')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreignId('user_id')
                ->nullable()
                ->comment('user koji je kreirao ili koji je poslednji obradio dokument...not null dok se ne bude sve podnosilo sa portala')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->string('path')
                ->comment('putanja do dokumenta')
                ->nullable();

            $table->string('location')
                ->comment('fizicka lokacija dokumenta')
                ->nullable();

            $table->text('note')
                ->nullable();

            $table->string('barcode')
                ->nullable();

            $table->text('metadata')
                ->comment('json')
                ->nullable();

            $table->morphs('documentable');

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
        Schema::dropIfExists('documents');
    }
}
