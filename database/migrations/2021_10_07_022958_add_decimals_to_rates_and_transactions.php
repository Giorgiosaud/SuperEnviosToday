<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDecimalsToRatesAndTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
        UPDATE `transactions`
          SET `amount`=`amount`*1000000
        ");
        DB::statement("
        UPDATE `rates`
          SET `amount`=`amount`*1000000
        ");
        DB::statement("
        UPDATE `balance_caches`
          SET `amount`=`amount`*1000000
        ");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("
        UPDATE `transactions`
          SET `amount`=`amount`/1000000
        ");
        DB::statement("
        UPDATE `rates`
          SET `amount`=`amount`/1000000
        ");
        DB::statement("
        UPDATE `balance_caches`
          SET `amount`=`amount`/1000000
        ");
    }
}
