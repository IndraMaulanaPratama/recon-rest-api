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
        Schema::connection('recon_auth')->create('CSCMOD_CLIENT', function (Blueprint $table) {
            $table->string('csm_c_id', 50)->primary();
            $table->string('csm_c_secret', 60)->nullable(true);
            $table->string('csm_c_name', 50)->nullable(false);
            $table->tinyInteger('csm_c_status')->default(0)->comment('0: Active, 1: Inactive');
            $table->string('csm_c_key', 64)->unique();
            $table->string('csm_c_bearer')->unique();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('recon_auth')->dropIfExists('CSCMOD_CLIENT');
    }
};
