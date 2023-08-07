<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('devel_report')->create('CSCCORE_BILLER_PRODUCT', function (Blueprint $table) {
            $table->uuid('CSC_BP_ID')->primary();
            $table->string('CSC_BP_PRODUCT', 100);
            $table->string('CSC_BP_BILLER', 50);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('devel_report')->dropIfExists('CSCCORE_BILLER_PRODUCT');
    }
};
