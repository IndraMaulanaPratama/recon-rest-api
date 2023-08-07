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
        Schema::connection('devel_report')->create('CSCCORE_CORRECTION', function (Blueprint $table) {
            $table->uuid('CSC_CORR_ID')->primary()->comment('Primary using UUID');
            $table->string('CSC_CORR_GROUP_TRANSFER', 50)->nullable(false)->comment('FK CSCCORE_GROUP_TRANSFER_FUNDS');
            $table->string('CSC_CORR_RECON_DANA_ID', 36)->nullable(false)->comment('FK RECON DANA');
            $table->string('CSC_CORR_DATE', 10)->nullable(false)->comment('yyy-mm-dd');
            $table->string('CSC_CORR_DATE_TRANSFER', 10)->nullable(false)->comment('yyy-mm-dd');
            $table->string('CSC_CORR_DATE_PINBUK, 10')->nullable(true)->comment('yyyy-mm-dd');
            $table->double('CSC_CORR_CORRECTION')->nullable(false);
            $table->char('CSC_CORR_CORRECTION_VALUE', 1)->nullable(true)->comment('P:Positive, N:Nrgative');
            $table->double('CSC_CORR_AMOUNT_TRANSFER')->nullable(true);
            $table->string('CSC_CORR_DESC', 100)->nullable(true);
            $table->tinyInteger('CSC_CORR_STATUS');
            $table->string('CSC_CORR_CREATED_BY', 50);
            $table->string('CSC_CORR_CREATED_DT', 19);
            $table->string('CSC_CORR_MODIFIED_BY', 50)->nullable(true);
            $table->string('CSC_CORR_MODIFIED_DT', 19)->nullable(true);
            $table->string('CSC_CORR_DELETED_BY', 50)->nullable(true);
            $table->string('CSC_CORR_DELETED_DT', 19)->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('devel_report')->dropIfExists('CSCCORE_CORRECTION');
    }
};
