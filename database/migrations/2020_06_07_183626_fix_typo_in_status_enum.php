<?php

use App\Models\PendingTransaction;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixTypoInStatusEnum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pending_transactions', function (Blueprint $table) {
            DB::statement("
                            ALTER TABLE `pending_transactions` 
                              MODIFY `status` 
                                ENUM('pending', 'aprooved','approved', 'rejected') 
                                  DEFAULT 'pending'
                                  "
            );
            PendingTransaction::where('status', 'aprooved')->update(['status' => 'approved']);
            DB::statement("
                            ALTER TABLE `pending_transactions`
                              MODIFY `status` 
                                ENUM('pending','approved', 'rejected') DEFAULT 'pending'
                                "
            );
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
            DB::statement("
                            ALTER TABLE `pending_transactions` 
                              MODIFY `status` 
                                ENUM('pending', 'aprooved','approved', 'rejected') DEFAULT 'pending'
                                "
            );
            PendingTransaction::where('status', 'approved')->update(['status' => 'aprooved']);
            DB::statement("
                            ALTER TABLE `pending_transactions` 
                              MODIFY `status` 
                                ENUM('pending','aprooved', 'rejected') DEFAULT 'pending'
                                "
            );
        });
    }
}
