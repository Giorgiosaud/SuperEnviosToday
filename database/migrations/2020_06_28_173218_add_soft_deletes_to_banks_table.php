<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddSoftDeletesToBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $badValues = DB::select('SELECT `id` FROM `banks` WHERE created_at=? Or updated_at=?', ['0000-00-00 00:00:00', '0000-00-00 00:00:00']);
        foreach ($badValues as $badValue) {
            DB::update("
                    UPDATE `banks` 
                      SET `created_at`= NOW(), `updated_at`= NOW() 
                        WHERE id=?",
              [$badValue->id]
            );
        }
        Schema::table('banks', function (Blueprint $table) {

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('banks', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
