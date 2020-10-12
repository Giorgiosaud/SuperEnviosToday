<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateAttachments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('attachments', function (Blueprint $table) {
        $table->index('attachable_id');
      });
      Schema::table('transactions', function (Blueprint $table) {
//        $table->index('old_id');
      });
      DB::statement("
                  UPDATE `attachments`
                    SET `attachable_type`='App\\\Models\\\Transaction'
		                  WHERE `attachments`.`attachable_type`='App\\\Transaction'
    ");
      DB::statement("
                  UPDATE `attachments`
                    JOIN `transactions`
                      ON `transactions`.`old_id`=`attachments`.`attachable_id`
                        SET `attachments`.`attachable_id`=`transactions`.`id`

    	");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      dump('cant downgrade');

        //
    }
}
