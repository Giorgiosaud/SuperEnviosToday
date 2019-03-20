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
            $table->bigInteger('amount');
            $table->unsignedInteger('from_account_id')->nullable();
            $table->unsignedInteger('to_account_id');
            $table->unsignedInteger('from_client_id')->nullable();
            $table->unsignedInteger('to_receiver_id')->nullable();
            $table->unsignedInteger('emitter_operator')->nullable();
            $table->enum('status',['assigned','in_progress','excecuted','confirmed','terminated'])->default('assigned');
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
