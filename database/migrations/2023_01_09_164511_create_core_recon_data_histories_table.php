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
        Schema::create('CSCCORE_RECON_DATA_HISTORY', function (Blueprint $table) {
            $table->uuid('CSC_RDTH_ID')
            ->comment('UUID');

            $table->string('CSC_RDTH_RECON_ID', 36)
            ->nullable(false)
            ->comment('FK Recon Data');

            $table->string('CSC_RDTH_PRODUCT', 100)
            ->nullable(false)
            ->comment('FK PRODUCT');

            $table->string('CSC_RDTH_CID', 7)
            ->nullable(false)
            ->comment('FK CID');

            $table->string('CSC_RDTH_TRX_DT', 10)
            ->nullable(false)
            ->comment('FK CSCCORE_RECON_DATA (yyyy-mm-dd)');

            $table->string('CSC_RDTH_SETTLED_DT', 19)
            ->nullable(false)
            ->comment('yyyy-mm-dd hh:mm:ss');

            $table->double('CSC_RDTH_NBILL')
            ->nullable(true);

            $table->double('CSC_RDTH_NMONTH')
            ->nullable(true);

            $table->double('CSC_RDTH_FEE')
            ->nullable(true);

            $table->double('CSC_RDTH_FEE_ADMIN')
            ->nullable(true);

            $table->double('CSC_RDTH_FEE_ADMIN_AMOUNT')
            ->nullable(true);

            $table->double('CSC_RDTH_FEE_BILLER')
            ->nullable(true);

            $table->double('CSC_RDTH_FEE_BILLER_AMOUNT')
            ->nullable(true);

            $table->double('CSC_RDTH_FEE_VSI')
            ->nullable(true);

            $table->double('CSC_RDTH_FEE_VSI_AMOUNT')
            ->nullable(true);

            $table->double('CSC_RDTH_BILLER_AMOUNT')
            ->nullable(true);

            $table->integer('CSC_RDTH_FORMULA_TRANSFER')
            ->nullable(true);

            $table->double('CSC_RDTH_CLAIM_VSI')
            ->nullable(true);

            $table->double('CSC_RDTH_CLAIM_VSI_AMOUNT')
            ->nullable(true);

            $table->double('CSC_RDTH_CLAIM_PARTNER')
            ->nullable(true);

            $table->double('CSC_RDTH_CLAIM_PARTNER_AMOUNT')
            ->nullable(true);

            $table->string('CSC_RDTH_USER_SETTLED', 50)
            ->nullable(true);

            $table->tinyInteger('CSC_RDTH_STATUS', false)
            ->nullable(true)
            ->comment('0: Settled, 1: Proses, 2: Belum Diproses');

            $table->string('CSC_RDTH_GENERATED_DT', 19)
            ->nullable(true)
            ->comment('yyyy-mm-dd hh:mm:ss');

            $table->double('CSC_RDTH_VERSION')
            ->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('CSCCORE_RECON_DATA_HISTORY');
    }
};
