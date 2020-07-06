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
        $badVaues = DB::select('select `id` from `banks` where created_at=? Or updated_at=?', ['0000-00-00 00:00:00', '0000-00-00 00:00:00']);
        foreach ($badVaues as $badVaue) {
            DB::update('update banks set created_at= NOW(), updated_at= NOW() where id=?', [$badVaue->id]);
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
