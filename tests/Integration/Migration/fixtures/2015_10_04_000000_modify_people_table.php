<?php

use QuantaForge\Database\Migrations\Migration;
use QuantaForge\Database\Schema\Blueprint;
use QuantaForge\Support\Facades\Schema;

class ModifyPeopleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('people', function (Blueprint $table) {
            $table->string('first_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('people', function (Blueprint $table) {
            $table->dropColumn('first_name');
        });
    }
}
