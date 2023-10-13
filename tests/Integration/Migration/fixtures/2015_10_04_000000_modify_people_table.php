<?php

use QuantaQuirk\Database\Migrations\Migration;
use QuantaQuirk\Database\Schema\Blueprint;
use QuantaQuirk\Support\Facades\Schema;

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
