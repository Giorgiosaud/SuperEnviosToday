<?php

use App\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateNewTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_transactions', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->unsignedInteger('account_id');
            $table->unsignedInteger('client_id')->nullable();
            $table->unsignedInteger('operator_id');
            $table->text('track_number')->nullable();
            $table->text('bank_reference')->nullable();
            $table->text('amount');
            $table->enum('status', ['executed', 'pending', 'in-progress']);
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("insert into new_transactions (account_id,client_id,operator_id,track_number,bank_reference,amount,status,created_at,updated_at,deleted_at) SELECT to_account_id AS 'account_id', client_id,to_user_id AS operator_id,id AS 'track_number' , transaction_number AS 'bank_reference' , amount,'executed' AS status, created_at,updated_at,deleted_at FROM transactions WHERE client_id=from_user_id AND type='income'");
        DB::statement("insert into new_transactions (account_id,client_id,operator_id,track_number,bank_reference,amount,status,created_at,updated_at,deleted_at) SELECT from_account_id AS 'account_id', NULL as client_id,from_user_id AS operator_id,related_transaction_id AS 'track_number' , transaction_number AS 'bank_reference' , amount*-1 as amount,'executed' AS status, created_at,updated_at,deleted_at FROM transactions WHERE from_user_id is not null AND type='outcome';
");
        DB::statement("insert into new_transactions (account_id,client_id,operator_id,track_number,bank_reference,amount,status,created_at,updated_at,deleted_at) SELECT to_account_id AS 'account_id', NULL as client_id,to_user_id AS operator_id,related_transaction_id AS 'track_number' , transaction_number AS 'bank_reference' , amount*-1 as amount,'executed' AS status, created_at,updated_at,deleted_at FROM transactions WHERE from_user_id is null AND type='outcome' and transaction_number is null and amount != 0 and related_transaction_id is not null;");
        DB::statement("insert into new_transactions (account_id,client_id,operator_id,track_number,bank_reference,amount,status,created_at,updated_at,deleted_at) SELECT to_account_id AS 'account_id', to_user_id as client_id,from_user_id AS operator_id,related_transaction_id AS 'track_number' , transaction_number AS 'bank_reference' , amount as amount,'executed' AS status, created_at,updated_at,deleted_at FROM transactions WHERE from_user_id is not null AND type='outcome';");

        $accounts=\App\Account::where('is_operator',true)->get();
        $roles=Role::find('coordinator')->first();
        $operator=$roles->users->first();
        foreach ($accounts as $account ){
            $actualBalance=round($account->oldBalance*10000);
            $amount=DB::table('new_transactions')->where('account_id', '=', $account->id)->sum('amount');
            $adjustDecimals=(floatval($amount)-intval($amount));
            if(abs($amount-$actualBalance)!=0) {
                DB::statement("insert into new_transactions (account_id,client_id,operator_id,track_number,bank_reference,amount,status,created_at,updated_at) values (?,?,?,?,?,?,?,?,?)", [$account->id, NULL, $operator->id, uniqid(), uniqid(), -$amount, 'executed', \Carbon\Carbon::now(), \Carbon\Carbon::now()]);
                if (abs($actualBalance) != 0) {
                    DB::statement("insert into new_transactions (account_id,client_id,operator_id,track_number,bank_reference,amount,status,created_at,updated_at) values (?,?,?,?,?,?,?,?,?)", [$account->id, NULL, $operator->id, uniqid(), uniqid(), $actualBalance, 'executed', \Carbon\Carbon::now(), \Carbon\Carbon::now()]);
                }
                if (abs($adjustDecimals) != 0) {
                    DB::statement("insert into new_transactions (account_id,client_id,operator_id,track_number,bank_reference,amount,status,created_at,updated_at) values (?,?,?,?,?,?,?,?,?)", [$account->id, NULL, $operator->id, uniqid(), uniqid(), -$adjustDecimals, 'executed', \Carbon\Carbon::now(), \Carbon\Carbon::now()]);
                }
            }
        }
                Schema::dropIfExists('transactions');

                Schema::table('new_transactions', function (Blueprint $table) {
                   $table->rename('transactions');
                });


        #DB::statement("insert into new_transactions (account_id,client_id,operator_id,track_number,bank_reference,amount,status,created_at,updated_at,deleted_at) SELECT to_account_id AS 'account_id', NULL as client_id,to_user_id AS operator_id,related_transaction_id AS 'track_number' , transaction_number AS 'bank_reference' , amount as amount,'executed' AS status, created_at,updated_at,deleted_at FROM transactions WHERE from_account_id=to_account_id AND related_transaction_id is null and type ='income' and status='terminated';");
        #DB::statement("insert into new_transactions (account_id,client_id,operator_id,track_number,bank_reference,amount,status,created_at,updated_at,deleted_at) SELECT to_account_id AS 'account_id', NULL as client_id,to_user_id AS operator_id,related_transaction_id AS 'track_number' , transaction_number AS 'bank_reference' , amount*-1 as amount,'executed' AS status, created_at,updated_at,deleted_at FROM transactions WHERE from_account_id=to_account_id AND related_transaction_id is null and type ='outcome' and status='terminated';");
        #DB::statement("insert into new_transactions (account_id,client_id,operator_id,track_number,bank_reference,amount,status,created_at,updated_at,deleted_at) SELECT to_account_id AS 'account_id', NULL as client_id,client_id AS operator_id,related_transaction_id AS 'track_number' , transaction_number AS 'bank_reference' , amount as amount,'executed' AS status, created_at,updated_at,deleted_at FROM transactions WHERE from_account_id IS Null AND transaction_number IS NULL AND related_transaction_id is null and type ='income' and status='terminated';");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('new_transactions');
    }
}
