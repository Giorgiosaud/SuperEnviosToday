<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateAttachablesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('attachables', function (Blueprint $table) {
      $table->unsignedInteger('attachment_id');
      $table->unsignedInteger('attachable_id');
      $table->string('attachable_type');
      $table->index(['attachment_id', 'attachable_id','attachable_type']);
    });
    DB::statement("
    INSERT INTO `attachables` (`attachment_id`,`attachable_id`,`attachable_type`) 
                              SELECT `id` AS 'attachment_id', `attachable_id`,`attachable_type`
                                FROM `attachments` 
                                  WHERE `attachable_id` IS NOT NULL
                                  ");
    Schema::table('attachments',function(Blueprint $table){
      $table->dropColumn('attachable_id');
      $table->dropColumn('attachable_type');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('attachables');
    Schema::table('attachments',function(Blueprint $table){
      $table->unsignedInteger('attachable_id');
      $table->string('attachable_type');
    });
  }
}
