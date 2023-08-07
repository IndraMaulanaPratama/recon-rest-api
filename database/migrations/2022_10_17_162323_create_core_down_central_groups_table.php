<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('devel_report')->create('CSCCORE_DOWN_CENTRAL_GROUP', function (Blueprint $table) {
            $table->string('CSC_DC_ID', 7);
            $table->string('CSC_DC_NAME', 100);
            $table->string('CSC_DC_PARENT', 7);
            $table->string('CSC_DC_PPID', 16);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('devel_report')->dropIfExists('CSCCORE_DOWN_CENTRAL_GROUP');
    }
};
