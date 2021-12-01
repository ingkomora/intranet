<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistryDepartmentUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('registry_department_units', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('label')
                ->comment('oznaka');
            $table->string('name');
            $table->foreignId('parent_id')->nullable()
                ->constrained('registry_department_units')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->text('note');
            $table->foreignId('status_id')
                ->constrained('statusi')
                ->onUpdate('cascade')
                ->onDelete('restrict');
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
        Schema::dropIfExists('registry_department_units');
    }
}
