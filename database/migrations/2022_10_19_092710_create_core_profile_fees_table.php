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
        Schema::connection('devel_report')->create('CSCCORE_PROFILE_FEE', function (Blueprint $table) {
            $table->string('CSC_PROFILE_ID', 50)->primary();
            $table->string('CSC_PROFILE_NAME', 50)->nullable(false);
            $table->string('CSC_PROFILE_DESC', 100)->nullable(false);
            $table->tinyInteger('CSC_PROFILE_DEFAULT')->nullable(false)->default(1)->comment('0:True, 1:False');
            $table->string('CSC_PROFILE_CREATED_BY', 50)->nullable(false);
            $table->string('CSC_PROFILE_CREATED_DT', 19)->nullable(false);
            $table->string('CSC_PROFILE_MODIFIED_BY', 50)->nullable(true);
            $table->string('CSC_PROFILE_MODIFIED_DT', 19)->nullable(true);
            $table->string('CSC_PROFILE_DELETED_BY', 50)->nullable(true);
            $table->string('CSC_PROFILE_DELETED_DT', 19)->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('devel_report')->dropIfExists('CSCCORE_PROFILE_FEE');
    }
};
