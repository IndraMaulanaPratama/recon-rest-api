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
        Schema::connection('devel_report')->create('CSCCORE_CALENDAR', function (Blueprint $table) {
            $table->string('CSC_CAL_ID', 20)->primary();
            $table->string('CSC_CAL_NAME', 100)->nullable(false);
            $table->tinyInteger('CSC_CAL_DEFAULT')->nullable(false)->default(1)->comment('0:True, 1:false');
            $table->string('CSC_CAL_CREATED_BY', 50)->nullable(false);
            $table->string('CSC_CAL_CREATED_DT', 19)->nullable(false);
            $table->string('CSC_CAL_MODIFIED_BY', 50)->nullable(true);
            $table->string('CSC_CAL_MODIFIED_DT', 19)->nullable(true);
            $table->string('CSC_CAL_DELETED_BY', 50)->nullable(true);
            $table->string('CSC_CAL_DELETED_DT', 19)->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('devel_report')->dropIfExists('CSCCORE_CALENDAR');
    }
};
