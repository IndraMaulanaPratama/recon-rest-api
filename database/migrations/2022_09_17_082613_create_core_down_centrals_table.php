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
        // CSC_DC_ID
        // CSC_DC_NAME
        // CSC_DC_ADDRESS
        // CSC_DC_PHONE
        // CSC_DC_PIC_NAME
        // CSC_DC_PIC_PHONE
        // CSC_DC_TYPE
        // CSC_DC_FUND_TYPE
        // CSC_DC_TERMINAL_TYPE
        // CSC_DC_REGISTERED
        // CSC_DC_ISBLOCKED
        // CSC_DC_MINIMAL_DEPOSIT
        // CSC_DC_SHORT_ID
        // CSC_DC_COUNTER_CODE
        // CSC_DC_A_ID
        // CSC_DC_ALIAS_NAME

        Schema::connection('devel_recon')->create('CSCCORE_DOWN_CENTRAL', function (Blueprint $table) {
            $table->string('CSC_DC_ID', 7)->primary();
            $table->string('CSC_DC_PROFILE', 50);
            $table->string('CSC_DC_NAME', 100);
            $table->string('CSC_DC_ADDRESS', 255);
            $table->string('CSC_DC_PHONE', 50);
            $table->string('CSC_DC_PIC_NAME', 100);
            $table->string('CSC_DC_PIC_PHONE', 100);
            $table->integer('CSC_DC_TYPE', false, 2)->default(0)->comment('0=Institution with no CENTRAL, 1=Institution with own CENTRAL');
            $table->integer('CSC_DC_FUND_TYPE', false, 2)->default('0');
            $table->string('CSC_DC_TERMINAL_TYPE', 4)->default('0000')->comment('6010 = Teller, 6011 = ATM, 6012 = POS, 6013 = AutoDebit/giralisasi, 6014 = Internet, 6015 = Kiosk, 6016 = Phone Banking / Call Center, 6017 = Mobile Banking, 6018 = EDC');
            $table->string('CSC_DC_REGISTERED', 19)->default('0000-00-00 00:00:00');
            $table->integer('CSC_DC_ISBLOCKED', false, 1)->default('0');
            $table->integer('CSC_DC_MINIMAL_DEPOSIT', false, 10);
            $table->string('CSC_DC_SHORT_ID', 3)->default('VSI');
            $table->string('CSC_DC_COUNTER_CODE', 1)->default('0');
            $table->string('CSC_DC_A_ID', 16)->nullable(true);
            $table->string('CSC_DC_ALIAS_NAME', 255)->default('');
            $table->string('CSC_DC_CREATED_BY', 50)->nullable(true);
            $table->string('CSC_DC_CREATED_DT', 19)->comment('Custom Timestamps with format yyyy-mm-dd h:i:s')->nullable(true);
            $table->string('CSC_DC_MODIFIED_BY', 50)->nullable(true);
            $table->string('CSC_DC_MODIFIED_DT', 19)->comment('Custom Timestamps with format yyyy-mm-dd h:i:s')->nullable(true);
            $table->string('CSC_DC_DELETED_BY', 50)->nullable(true);
            $table->string('CSC_DC_DELETED_DT', 19)->comment('Custom Timestamps with format yyyy-mm-dd h:i:s')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('devel_recon')->dropIfExists('CSCCORE_DOWN_CENTRAL');
    }
};
