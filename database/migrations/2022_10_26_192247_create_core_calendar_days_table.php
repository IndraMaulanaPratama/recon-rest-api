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
        Schema::connection('devel_report')->create('CSCCORE_CALENDAR_DAYS', function (Blueprint $table) {
            $table->uuid('CSC_CD_ID')->primary();
            $table->string('CSC_CD_CALENDAR', 20);
            $table->string('CSC_CD_DATE', 10);
            $table->string('CSC_CD_DESC', 50);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('devel_report')->dropIfExists('CSCCORE_CALENDAR_DAYS');
    }
};
