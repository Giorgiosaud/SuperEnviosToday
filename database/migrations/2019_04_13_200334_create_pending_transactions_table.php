<?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreatePendingTransactionsTable extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create('pending_transactions', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('client_id');
                $table->unsignedInteger('foreign_account_id');
                $table->unsignedInteger('received_transaction_attachment_id')->nullable();
                $table->unsignedInteger('receiver_account_id');
                $table->unsignedInteger('venezuelan_operator_account_id');
                $table->unsignedInteger('rate');
                $table->unsignedInteger('amount');
                $table->enum('status', ['pending', 'aprooved', 'rejected'])->default('pending');
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
            Schema::dropIfExists('pending_transactions');
        }
    }
