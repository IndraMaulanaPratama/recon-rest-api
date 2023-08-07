<?php

use Egulias\EmailValidator\Parser\Comment;
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

        Schema::connection('devel_report')->create('CSCCORE_BANK', function (Blueprint $table) {
            $table->integer('CSC_BANK_CODE', false)->primary();
            $table->string('CSC_BANK_NAME', 50);
            $table->string('CSC_BANK_CREATED_BY', 50);
            $table->string('CSC_BANK_CREATED_DT', 19);
            $table->string('CSC_BANK_MODIFIED_BY', 50)->nullable(true);
            $table->string('CSC_BANK_MODIFIED_DT', 19)->nullable(true);
            $table->string('CSC_BANK_DELETED_BY', 50)->nullable(true);
            $table->string('CSC_BANK_DELETED_DT', 19)->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('devel_report')->dropIfExists('CSCCORE_BANK');
    }
};