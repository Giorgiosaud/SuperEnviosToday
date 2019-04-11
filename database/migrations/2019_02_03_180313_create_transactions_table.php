<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('from_account_id')->nullable();
            $table->unsignedInteger('to_account_id');
            $table->unsignedInteger('related_transaction_id')->nullable();
            $table->bigInteger('amount');
            $table->string('url_attachment')->nullable();
            $table->enum('status',['pending','assigned','in_progress','executed','confirmed','terminated'])->default('pending');
            $table->enum('type', ['income','outcome','cancelled','pqc'])->default('outcome');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
