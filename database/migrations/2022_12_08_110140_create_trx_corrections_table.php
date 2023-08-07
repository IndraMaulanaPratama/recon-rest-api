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
        Schema::connection('devel_report')->create('trx_corrections', function (Blueprint $table) {
            $table->uuid('CSM_TC_ID');
            $table->string('CSM_TC_RECON_ID', 36);
            $table->string('CSM_TC_RECON_DANA_ID', 36);
            $table->string('CSM_TC_PRODUCT', 100);
            $table->string('CSM_TC_CID', 7);
            $table->string('CSM_TC_SUBID', 20);
            $table->string('CSM_TC_TRX_DT', 10);
            $table->string('CSM_TC_PROCESS_DT', 19);
            $table->double('CSM_TC_NBILL');
            $table->double('CSM_TC_NMONTH');
            $table->double('CSM_TC_FEE');
            $table->double('CSM_TC_FEE_ADMIN');
            $table->double('CSM_TC_FEE_ADMIN_AMOUNT');
            $table->double('CSM_TC_FEE_BILLER');
            $table->double('CSM_TC_FEE_BILLER_AMOUNT');
            $table->double('CSM_TC_VSI');
            $table->double('CSM_TC_VSI_AMOUNT');
            $table->double('CSM_TC_BILLER_AMOUNT');
            $table->integer('CSM_TC_FORMULA_TRANSFER');
            $table->double('CSM_TC_CLAIM_VSI');
            $table->double('CSM_TC_CLAIM_VSI_AMOUNT');
            $table->double('CSM_TC_CLAIM_PARTNER');
            $table->double('CSM_TC_CLAIM_PARTNER_AMOUNT');
            $table->string('CSM_TC_SW_REFNUM', 32);
            $table->tinyInteger('CSM_TC_STATUS_TRX');
            $table->tinyInteger('CSM_TC_STATUS_DATA');
            $table->tinyInteger('CSM_TC_STATUS_FUNDS');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trx_corrections');
    }
};
