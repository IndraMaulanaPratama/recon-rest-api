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
        Schema::connection('devel_report')->create('CSCCORE_EXCLUDE_PARTNER', function (Blueprint $table) {
            $table->uuid('CSC_EP_ID')->primary();
            $table->string('CSC_EP_CID', 7);
            $table->string('CSC_EP_PRODUCT', 100);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('devel_report')->dropIfExists('CSCCORE_EXCLUDE_PARTNER');
    }
};
