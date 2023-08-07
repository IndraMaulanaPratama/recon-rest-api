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
        Schema::connection('devel_report')->create('CSCCORE_TRANSACTION_DEFINITION', function (Blueprint $table) {
            $table->string('CSC_TD_NAME', 100)->nullable(false)->default('')->primary();
            $table->string('CSC_TD_GROUPNAME', 100)->nullable(false)->default('');
            $table->string('CSC_TD_ALIASNAME', 100)->nullable(false)->default('');
            $table->string('CSC_TD_DESC', 100)->nullable(false)->default('');
            $table->string('CSC_TD_TABLE', 50)->nullable(false)->default('');
            $table->string('CSC_TD_CRITERIA', 255)->nullable(false)->default('');
            $table->string('CSC_TD_FINDCRITERIA', 100)->nullable(false)->default('');
            $table->string('CSC_TD_BANK_CRITERIA', 250)->nullable(false)->default('');
            $table->string('CSC_TD_CENTRAL_CRITERIA', 250)->nullable(false)->default('');
            $table->string('CSC_TD_BANK_COLUMN', 100)->nullable(false)->default('');
            $table->string('CSC_TD_CENTRAL_COLUMN', 100)->nullable(false)->default('');
            $table->string('CSC_TD_TERMINAL_COLUMN', 100)->nullable(false)->default('');
            $table->string('CSC_TD_SUBID_COLUMN', 20)->nullable(true);
            $table->string('CSC_TD_SUBNAME_COLUMN', 25)->nullable(true);
            $table->string('CSC_TD_SWITCH_REFNUM_COLUMN', 100)->nullable(true);
            $table->string('CSC_TD_SWITCH_PAYMENT_REFNUM_COLUMN', 100)->nullable(true);
            $table->string('CSC_TD_DATE_COLUMN', 100)->nullable(false)->default('');
            $table->string('CSC_TD_NREK_COLUMN', 100)->nullable(false)->default('');
            $table->string('CSC_TD_NBILL_COLUMN', 100)->nullable(false)->default('');
            $table->string('CSC_TD_BILL_AMOUNT_COLUMN', 100)->nullable(false)->default('');
            $table->string('CSC_TD_ADM_AMOUNT_COLUMN', 100)->nullable(false)->default('');
            $table->string('CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_0', 100)->nullable(false)->default('0 as Decuti');
            $table->string('CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_1', 100)->nullable(false)->default('0 as Decuti');
            $table->string('CSC_TD_ADM_AMOUNT_COLUMN_DEDUCTION_2', 100)->nullable(false)->default('0 as Decuti');
            $table->string('CSC_TD_TABLE_ARCH', 50)->nullable(false)->default('');
            $table->string('CSC_TD_BANK_GROUPBY', 50)->nullable(false)->default('');
            $table->string('CSC_TD_CENTRAL_GROUPBY', 100)->nullable(false)->default('');
            $table->string('CSC_TD_TERMINAL_GROUPBY', 50)->nullable(false)->default('');
            $table->string('CSC_TD_TYPE_TRX', 100)->nullable(false)->default('+');
            $table->tinyInteger('CSC_TD_ISACTIVE')->nullable(false)->default(1);
            $table->string('CSC_TD_CREATED_BY', 50)->nullable(true);
            $table->string('CSC_TD_CREATED_DT', 19)->comment('Custom Timestamps with format yyyy-mm-dd h:i:s')->nullable(true);
            $table->string('CSC_TD_MODIFIED_BY', 50)->nullable(true);
            $table->string('CSC_TD_MODIFIED_DT', 19)->comment('Custom Timestamps with format yyyy-mm-dd h:i:s')->nullable(true);
            $table->string('CSC_TD_DELETED_BY', 50)->nullable(true);
            $table->string('CSC_TD_DELETED_DT', 19)->comment('Custom Timestamps with format yyyy-mm-dd h:i:s')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('devel_report')->dropIfExists('CSCCORE_TRANSACTION_DEFINITION');
    }
};
