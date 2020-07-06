<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddUniqueToRoleUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $duplicatedValues=DB::select('select `user_id`,`role_name_id` ,count(`role_name_id`) from `role_user` GROUP BY `user_id`,`role_name_id` HAVING COUNT(`role_name_id`) > 1');
        foreach($duplicatedValues as $duplicateValue){
            DB::delete('delete from role_user where user_id=? and role_name_id=? Limit 1',[$duplicateValue->user_id,$duplicateValue->role_name_id]);
        }
        Schema::table('role_user', function (Blueprint $table) {
            $table->unique(['user_id','role_name_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('role_user', function(Blueprint $table)
        {
            $table->dropUnique(['user_id','role_name_id']);
        });
    }
}
