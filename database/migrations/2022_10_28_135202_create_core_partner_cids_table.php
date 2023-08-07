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
        Schema::connection('devel_report')->create('CSCCORE_PARTNER_CID', function (Blueprint $table) {
            $table->uuid('CSC_PC_ID')->primary();
            $table->string('CSC_PC_CID');
            $table->string('CSC_PC_PARTNER');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('devel_report')->dropIfExists('CSCCORE_PARTNER_CID');
    }
};
