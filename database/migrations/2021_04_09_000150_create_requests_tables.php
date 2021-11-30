<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestsTables extends Migration
{
//    ovo renamovati iz 2021_04_09_000150_create_zahtevi_table => ***2021_04_09_000150_create_requests_tables i dodati u migration table
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        create_request_category_types_table
        Schema::create('request_category_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('note')->nullable();
            $table->foreignId('status_id')->default(1)->constrained('statusi')->onUpdate('cascade')->onDelete('restrict');
            $table->text('note')->nullable();
            $table->morphs('requestable');
            $table->timestamps();
        });
//        create_request_categories_table
        Schema::create('request_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('note')->nullable();
            $table->foreignId('request_category_type_id')->constrained('request_category_types')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('status_id')->default(1)->constrained('statusi')->onUpdate('cascade')->onDelete('restrict');
            $table->timestamps();
        });
        Schema::dropIfExists('zahtevi');
//        create_requests_table
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->string('osoba_id',13);
            $table->foreign('osoba_id')->references('id')->on('tosoba')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('request_category_id')->constrained('request_categories')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('status_id')->default(1)->constrained('statusi')->onUpdate('cascade')->onDelete('restrict');
            $table->text('note')->nullable();
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
        Schema::dropIfExists('requests');
        Schema::dropIfExists('request_categories');
        Schema::dropIfExists('request_category_types');
        //
    }
}
