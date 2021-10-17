<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnRenumerationTableContractCurrentStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contract_current_status', function (Blueprint $table) {
            $table->renameColumn('renumeration','remuneration');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contract_current_status', function (Blueprint $table) {
            $table->renameColumn('remuneration','renumeration');
        });
    }
}
