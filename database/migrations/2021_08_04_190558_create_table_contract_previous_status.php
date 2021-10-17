<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableContractPreviousStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract_last_status', function (Blueprint $table) {
            $table->integer('contract_id')->index();
            $table->string('contractor_bulstat');
            $table->string('contractor_name');
            $table->string('individual_eik');
            $table->string('individual_names');
            $table->string('start_date');
            $table->string('last_amend_date');
            $table->string('end_date');
            $table->string('reason');
            $table->string('time_limit');
            $table->string('eco_code');
            $table->string('profession_code');
            $table->string('remuneration');
            $table->string('profession_name');
            $table->string('ekatte_code');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contract_last_status');
    }
}
