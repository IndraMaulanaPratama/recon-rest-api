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
        Schema::connection('server_report')->create('CSCCORE_MODULE_DEFINITION', function (Blueprint $table) {
            $table->string('CSC_MD_GROUPNAME', 100)->primary();
            $table->string('CSC_MD_TABLE', 50);
            $table->string('CSC_MD_ALIASNAME', 100);
            $table->string('CSC_MD_DESC', 100);
            $table->string('CSC_MD_BILLER_COLUMN', 100);
            $table->text('CSC_MD_CRITERIA');
            $table->text('CSC_MD_FINDCRITERIA');
            $table->string('CSC_MD_BANK_CRITERIA', 250);
            $table->string('CSC_MD_CENTRAL_CRITERIA', 250);
            $table->string('CSC_MD_BANK_COLUMN', 100);
            $table->string('CSC_MD_CENTRAL_COLUMN', 100);
            $table->string('CSC_MD_TERMINAL_COLUMN', 100);
            $table->string('CSC_MD_SUBID_COLUMN', 100);
            $table->string('CSC_MD_SUBNAME_COLUMN', 100);
            $table->string('CSC_MD_SWITCH_REFNUM_COLUMN', 100);
            $table->string('CSC_MD_SWITCH_PAYMENT_REFNUM_COLUMN', 100);
            $table->string('CSC_MD_DATE_COLUMN', 100);
            $table->string('CSC_MD_NREK_COLUMN', 100);
            $table->string('CSC_MD_NBILL_COLUMN', 100);
            $table->string('CSC_MD_BILL_AMOUNT_COLUMN', 100);
            $table->string('CSC_MD_NREK_COLUMN', 100);
            $table->string('CSC_MD_ADM_AMOUNT_COLUMN_DEDUCTION_0', 100);
            $table->string('CSC_MD_ADM_AMOUNT_COLUMN_DEDUCTION_1', 100);
            $table->string('CSC_MD_ADM_AMOUNT_COLUMN_DEDUCTION_2', 100);
            $table->string('CSC_MD_TABLE_ARCH', 50);
            $table->string('CSC_MD_BANK_GROUPBY', 50);
            $table->string('CSC_MD_CENTRAL_GROUPBY', 50);
            $table->string('CSC_MD_TERMINAL_GROUPBY', 50);
            $table->string('CSC_MD_TYPE_TRX', 100);
            $table->smallInteger('CSC_MD_ISACTIVE', false);
            $table->string('CSC_MD_CREATED_DT', 19);
            $table->string('CSC_MD_CREATED_By', 50);
            $table->string('CSC_MD_MODIFIED_DT', 19);
            $table->string('CSC_MD_MODIFIED_By', 50);
            $table->string('CSC_MD_DELETED_DT', 19);
            $table->string('CSC_MD_DELETED_By', 50);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('server_report')->dropIfExists('CSCCORE_MODULE_DEFINITION');
    }
};
