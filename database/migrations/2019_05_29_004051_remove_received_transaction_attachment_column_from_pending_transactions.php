<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveReceivedTransactionAttachmentColumnFromPendingTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pending_transactions', function (Blueprint $table) {
            $table->dropColumn('received_transaction_attachment_id');
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
            $table->unsignedInteger('received_transaction_attachment_id')->nullable;
        });
    }
}
