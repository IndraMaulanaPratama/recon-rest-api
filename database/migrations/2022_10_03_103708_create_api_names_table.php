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
        Schema::connection('recon_auth')->create('CSCMOD_API_NAME', function (Blueprint $table) {
            $table->string('CSM_AN_ID', 255)->primary();
            $table->string('CSM_AN_DESC', 255)->nullable(true);
            $table->string('CSM_AN_CREATED_DT', 19)->comment('Custom Timestamps with format yyyy-mm-dd h:i:s');
            $table->string('CSM_AN_DELETED_DT', 19)->nullable(true)->default(null)->comment('Custom Timestamps with format yyyy-mm-dd h:i:s');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('recon_auth')->dropIfExists('CSCMOD_API_NAME');
    }
};
