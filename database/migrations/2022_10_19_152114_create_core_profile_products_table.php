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
        Schema::connection('devel_report')->create('CSCCORE_PROFILE_PRODUCT', function (Blueprint $table) {
            $table->uuid('CSC_PP_ID')->primary();
            $table->string('CSC_PP_PROFILE', 50)->nullable(false)->comment('FK CSCORE_PROFILE_FEE');
            $table->string('CSC_PP_PRODUCT', 100)->nullable(false)->comment('FK CSCCORE_TRANSACTION_DEFINITION');
            $table->integer('CSC_PP_FORMULA_TRANSFER', false)->nullable(false)->comment('FK CSCCORE_FORMULA_TRANSFER');
            $table->double('CSC_PP_FEE_ADMIN');
            $table->double('CSC_PP_FEE_BILLER');
            $table->double('CSC_PP_FEE_VSI');
            $table->double('CSC_PP_CLAIM_PARTNER');
            $table->double('CSC_PP_CLAIM_VSI');
            $table->tinyInteger('CSC_PP_MULTIPLE_TYPE')->comment('0:Jumlah Rekening, 1:Jumlah Lembar');
            $table->tinyInteger('CSC_PP_PARTNER_BILLING_TYPE')->comment('0:VSI Tagihan ke mitra, 1:Mitra Tagihan ke VSI');
            $table->tinyInteger('CSC_PP_BILLER_BILLING_TYPE')->comment('0:Jumla VSI Tagihan ke biller, 1:Biller Tagihan ke VSI');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('devel_report')->dropIfExists('CSCCORE_PROFILE_PRODUCT');
    }
};
