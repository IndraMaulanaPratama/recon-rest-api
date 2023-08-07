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
        Schema::connection('devel_report')->create('CSCCORE_ACCOUNT', function (Blueprint $table) {
            $table->string('CSC_ACCOUNT_NUMBER', 20)->primary();
            $table->integer('CSC_ACCOUNT_BANK', false)->nullable(false);
            $table->string('CSC_ACCOUNT_NAME', 50)->nullable(false);
            $table->string('CSC_ACCOUNT_OWNER', '50')->nullable(false);
            $table->tinyInteger('CSC_ACCOUNT_TYPE', false)->nullable(false)->default(1)->comment('0: Internal, 1: External');
            $table->string('CSC_ACCOUNT_CREATED_BY', 50)->nullable(false);
            $table->string('CSC_ACCOUNT_CREATED_DT', 19)->nullable(false);
            $table->string('CSC_ACCOUNT_MODIFIED_BY', 50)->nullable(true);
            $table->string('CSC_ACCOUNT_MODIFIED_DT', 19)->nullable(true);
            $table->string('CSC_ACCOUNT_DELETED_BY', 50)->nullable(true);
            $table->string('CSC_ACCOUNT_DELETED_DT', 19)->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('devel_report')->dropIfExists('CSCCORE_ACCOUNT');
    }
};
