<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembershipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->string('osoba_id', 13);
            $table->date('started_at')->nullable()->comment('datumuo, tj. rokzanaplatu iz clanarine');
            $table->date('ended_at')->nullable();
            $table->boolean('active')->default(0);
            $table->foreignId('status_id')->constrained('statusi')->cascadeOnUpdate()->restrictOnDelete();
            $table->text('note')->nullable();
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
        Schema::dropIfExists('memberships');
    }
}
