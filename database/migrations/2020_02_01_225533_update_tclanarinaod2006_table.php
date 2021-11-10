<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTclanarinaod2006Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tclanarinaod2006', function (Blueprint $table) {
            $table->date('datumuplate')->nullable();
            $table->timestamp('created_at')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tclanarinaod2006', function (Blueprint $table) {
            $table->dropColumn('datumuplate');
            $table->dropColumn('created_at');
        });
    }
}
