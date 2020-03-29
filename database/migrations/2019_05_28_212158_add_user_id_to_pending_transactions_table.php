<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToPendingTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pending_transactions', function (Blueprint $table) {
            $table->unsignedInteger('foreign_id');
            $table->unsignedInteger('receiver_id');
            $table->unsignedInteger('venezuelan_operator_id');
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
            $table->dropColumn('foreign_id');
            $table->dropColumn('receiver_id');
            $table->dropColumn('venezuelan_operator_id');
        });
    }
}
