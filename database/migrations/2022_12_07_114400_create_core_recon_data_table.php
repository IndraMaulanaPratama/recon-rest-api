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
        Schema::connection('devel_report')->create('CSCCORE_RECON_DATA', function (Blueprint $table) {
            $table->uuid('CSC_RDT_ID')->primary()->comment('UUID');
            $table->string('CSC_RDT_RECON_DANA_ID', 36)->comment('FK Recon Dana');
            $table->string('CSC_RDT_PRODUCT', 100)->comment('FK CSCCORE_TRANSACTION_DEFINITION');
            $table->string('CSC_RDT_CID', 7)->comment('FK CSCCORE_DOWN_CENTRAL');
            $table->string('CSC_RDT_TRX_DT', 10)->comment('yyyy-mm-dd');
            $table->string('CSC_RDT_SETTLED_DT', 19)->comment('yyyy-mm-dd hh:mm:ss');
            $table->double('CSC_RDT_NBILL');
            $table->double('CSC_RDT_NMONTH');
            $table->double('CSC_RDT_FEE');
            $table->double('CSC_RDT_FEE_ADMIN');
            $table->double('CSC_RDT_FEE_ADMIN_AMOUNT');
            $table->double('CSC_RDT_FEE_BILLER');
            $table->double('CSC_RDT_FEE_BILLER_AMOUNT');
            $table->double('CSC_RDT_FEE_VSI');
            $table->double('CSC_RDT_FEE_VSI_AMOUNT');
            $table->double('CSC_RDT_BILLER_AMOUNT');
            $table->double('CSC_RDT_FORMULA_TRANSFER');
            $table->double('CSC_RDT_CLAIM_VSI');
            $table->double('CSC_RDT_CLAIM_VSI_AMOUNT');
            $table->double('CSC_RDT_CLAIM_PARTNER');
            $table->double('CSC_RDT_CLAIM_PARTNER_AMOUNT');
            $table->string('CSC_RDT_USER_SETTLED', 50);
            $table->tinyInteger('CSC_RDT_STATUS', false)->default(2)
            ->comment('0: Settled, 1: Proses, 2: Belum Diproses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('devel_report')->dropIfExists('CSCCORE_RECON_DATA');
    }
};
