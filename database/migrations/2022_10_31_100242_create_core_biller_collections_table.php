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
        Schema::connection('devel_report')->create('CSCCORE_BILLER_COLLECTION', function (Blueprint $table) {
            $table->uuid('CSC_BC_ID');
            $table->string('CSC_BC_GROUP_BILLER', 20);
            $table->string('CSC_BC_BILLER', 5);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('devel_report')->dropIfExists('CSCCORE_BILLER_COLLECTION');
    }
};
