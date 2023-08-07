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
        Schema::connection('devel_report')->create('CSCCORE_FORMULA_TRANSFER', function (Blueprint $table) {
            $table->integer('CSC_FH_ID', true);
            $table->string('CSC_FH_FORMULA', 255)->nullable(false);
            $table->tinyInteger('CSC_FH_STATUS', false)->nullable(false)->comment('0: Active, 1: Inactive');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('devel_report')->dropIfExists('CSCCORE_FORMULA_TRANSFER');
    }
};
