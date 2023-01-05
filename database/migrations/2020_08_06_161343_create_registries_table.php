<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('registries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('base_number')
                ->comment('osnovni broj');
            $table->boolean('copy')
                ->default(0)
                ->comment('prepis');
            $table->string('subject')
                ->comment('predmet');
            $table->integer('sub_base_number')
                ->nullable()
                ->comment('pod_broj');
            $table->foreignId('registry_department_unit_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->integer('counter')
                ->default(0)
                ->comment('brojac');
            $table->foreignId('status_id')
                ->default(1)
                ->constrained('statusi')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->integer('year');
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
        Schema::dropIfExists('registries');
    }
}
