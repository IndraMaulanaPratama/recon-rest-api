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
        Schema::connection('recon_auth')->create('CSCMOD_CLIENT_API_SCOPE', function (Blueprint $table) {
            $table->uuid('CSM_CAS_ID')->primary();
            $table->string('CSM_CAS_CLIENT', 50);
            $table->string('CSM_CAS_API_NAME', 255);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('recon_auth')->dropIfExists('CSCMOD_CLIENT_API_SCOPE');
    }
};
