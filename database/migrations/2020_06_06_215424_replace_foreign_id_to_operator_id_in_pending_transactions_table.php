<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReplaceForeignIdToOperatorIdInPendingTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pending_transactions', function (Blueprint $table) {
            $table->renameColumn('foreign_id', 'operator_id');
            $table->renameColumn('foreign_account_id', 'operator_account_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pending_transactions', function (Blueprint $table) {
            $table->renameColumn('operator_id', 'foreign_id');
            $table->renameColumn('operator_account_id', 'foreign_account_id');

        });
    }
}
