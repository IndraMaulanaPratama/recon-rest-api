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
        Schema::connection('devel_report')->create('CSCCORE_GROUP_OF_PRODUCT', function (Blueprint $table) {
            $table->string('CSC_GOP_PRODUCT_NAME', 255)->primary();
            $table->string('CSC_GOP_PRODUCT_GROUP', 50);
            $table->string('CSC_GOP_PRODUCT_PARENT_PRODUCT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('devel_report')->dropIfExists('CSCCORE_GROUP_OF_PRODUCT');
    }
};
