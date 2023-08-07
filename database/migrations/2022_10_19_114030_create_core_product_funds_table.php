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
        Schema::connection('devel_report')->create('CSCCORE_PRODUCT_FUNDS', function (Blueprint $table) {
            $table->uuid('CSC_PF_ID')->primary();
            $table->string('CSC_PF_PRODUCT', 100)->nullable(false);
            $table->string('CSC_PF_GROUP_TRANSFER', 50)->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('devel_report')->dropIfExists('CSCCORE_PRODUCT_FUNDS');
    }
};
