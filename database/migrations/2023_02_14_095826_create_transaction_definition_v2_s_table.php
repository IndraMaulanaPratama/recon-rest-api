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
        Schema::connection('server_report')
        ->create('CSCCORE_TRANSACTION_DEFINITION_V2', function (Blueprint $table) {
            $table->string('CSC_TD_NAME', 100)->primary();
            $table->string('CSC_TD_BILLER_ID', 255)->nullable(true);
            $table->string('CSC_TD_GROUPNAME', 100);
            $table->string('CSC_TD_ALIASNAME');
            $table->string('CSC_TD_DESC');
            $table->text('CSC_TD_FINDCRITERIA');
            $table->string('CSC_TD_PAN', 5);
            $table->string('CSC_TD_CREATED_DT', 19);
            $table->string('CSC_TD_CREATED_BY', 50);
            $table->string('CSC_TD_MODIFIED_DT', 19);
            $table->string('CSC_TD_MODIFIED_BY', 50);
            $table->string('CSC_TD_DELETED_DT', 19);
            $table->string('CSC_TD_DELETED_BY', 50);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('server_report')->dropIfExists('CSCCORE_TRANSACTION_DEFINITION_V2');
    }
};
