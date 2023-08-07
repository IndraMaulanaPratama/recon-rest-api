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
        Schema::connection('devel_report')->create('CSCCORE_RECON_DANA', function (Blueprint $table) {
            $table->uuid('CSC_RDN_ID');
            $table->string('CSC_RDN_BILLER', 5);
            $table->string('CSC_GROUP_TRANSFER', 50);
            $table->string('CSC_RDN_START_DT', 10);
            $table->string('CSC_RDN_END_DT', 10);
            $table->string('CSC_RDN_SETTLED_DT', 10);
            $table->string('CSC_RDN_DSEC_TRANSFER', 20);
            $table->double('CSC_RDN_AMOUNT');
            $table->double('CSC_RDN_SUSPECT_PROCESS');
            $table->char('CSC_RDN_SUSPECT_PROCESS_VALUE');
            $table->double('CSC_RDN_SUSPECT_UNPROCESS');
            $table->char('CSC_RDN_SUSPECT_UNPROCESS_VALUE');
            $table->double('CSC_RDN_CORRECTION_PROCESS');
            $table->char('CSC_RDN_CORRECTION_PROCESS_VALUE');
            $table->double('CSC_RDN_CORRECTION_UNPROCESS');
            $table->char('CSC_RDN_CORRECTION_UNPROCESS_VALUE');
            $table->double('CSC_RDN_AMOUNT_TRANSFER');
            $table->double('CSC_RDN_REAL_TRANSFER');
            $table->double('CSC_RDN_DIFF_TRANSFER');
            $table->tinyInteger('CSC_RDN_TYPE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('devel_report')->dropIfExists('CSCCORE_RECON_DANA');
    }
};
