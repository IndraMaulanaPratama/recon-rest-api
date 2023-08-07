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
        Schema::connection('devel_report')->create('CSCCORE_GROUP_BILLER', function (Blueprint $table) {
            $table->string('CSC_GB_ID', 20)->primary();
            $table->string('CSC_GB_NAME', 100);
            $table->string('CSC_GB_CREATED_BY', 50);
            $table->string('CSC_GB_CREATED_DT', 19);
            $table->string('CSC_GB_MODIFIED_BY', 50)->nullable(true);
            $table->string('CSC_GB_MODIFIED_DT', 19)->nullable(true);
            $table->string('CSC_GB_DELETED_BY', 50)->nullable(true);
            $table->string('CSC_GB_DELETED_DT', 19)->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('devel_report')->dropIfExists('CSCCORE_GROUP_BILLER');
    }
};
