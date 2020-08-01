<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedInteger('from_user_id')->nullable();
            $table->unsignedInteger('to_user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'from_user_id')) {
                $table->dropColumn('from_user_id');
            }
            if (Schema::hasColumn('transactions', 'to_user_id')) {
                $table->dropColumn('to_user_id');
            }
        });
    }
}
