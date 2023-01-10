<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateRequestsExternalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requests_external', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('request_category_id');
            $table->bigInteger('status_id');
            $table->string('note')->nullable();
            $table->bigInteger('requestable_id')->nullable();
            $table->string('requestable_type')->nullable();
            $table->timestamps();

            $table->foreign('request_category_id')
                ->references('id')
                ->on('request_categories')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('status_id')
                ->references('id')
                ->on('statusi')
                ->onUpdate('cascade')
                ->onDelete('restrict');

        });

        DB::statement("COMMENT ON TABLE requests_external IS 'Tabela namenjena za cuvanje podataka o zahtevima koji nisu vezani za specificnu osobu upisanu u tosoba tabelu.';");


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requests_external');
    }
}
