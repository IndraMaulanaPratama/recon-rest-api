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
        Schema::connection('devel_report')->create('CSCCORE_GROUP_TRANSFER_FUNDS', function (Blueprint $table) {
            $table->string('CSC_GTF_ID', 50)->primary();
            $table->integer('CSC_GTF_SOURCE', false);
            $table->integer('CSC_GTF_DESTINATION', false);
            $table->string('CSC_GTF_NAME', 100);
            $table->tinyInteger('CSC_GTF_TRANSFER_TYPE', false)->default(0)->comment('0: Pelimpahan, 1: Deposit');
            $table->integer('CSC_GTF_PRODUCT_COUNT', false)->nullable(true);
            $table->string('CSC_GTF_CREATED_BY', 50);
            $table->string('CSC_GTF_CREATED_DT', 19);
            $table->string('CSC_GTF_MODIFIED_BY', 50)->nullable(true);
            $table->string('CSC_GTF_MODIFIED_DT', 19)->nullable(true);
            $table->string('CSC_GTF_DELETED_BY', 50)->nullable(true);
            $table->string('CSC_GTF_DELETED_DT', 19)->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('devel_report')->dropIfExists('CSCCORE_GROUP_TRANSFER_FUNDS');
    }
};
