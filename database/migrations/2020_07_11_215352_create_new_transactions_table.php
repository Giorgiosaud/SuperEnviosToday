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
        $accounts = DB::table('accounts')->where('is_operator', true)->get();
        $operator = DB::selectOne("SELECT users.name,users.id, roles.`name_id` from users join `role_user` on `role_user`.`user_id`=users.id join `roles` on roles.`name_id`=`role_user`.`role_name_id` where name_id='coordinator'");
        if ($accounts) {
            foreach ($accounts as $account) {
                $outcome = DB::selectOne("SELECT sum(amount) AS amount FROM transactions WHERE from_account_id = ? AND from_account_id IS NOT NULL and type = 'outcome' GROUP BY from_account_id", [$account->id]);
                $income = DB::selectOne("SELECT sum(amount) AS amount from transactions where to_account_id = ? AND to_account_id IS NOT NULL and type = 'income' GROUP BY to_account_id", [$account->id]);
                $incomeAmount = $income ? $income->amount : 0;
                $outcomeAmount = $outcome ? $outcome->amount : 0;
                $migrationBalance = intval(($incomeAmount - $outcomeAmount), 0);
                $sum = DB::selectOne('select sum(amount) as amount from new_transactions where account_id=? GROUP BY `account_id`', [$account->id]);
                if ($sum) {
                    $balance = $sum->amount;
                    while ($balance != 0) {
                        DB::statement("insert into new_transactions (account_id,client_id,operator_id,track_number,bank_reference,amount,status,comment,created_at,updated_at) values (?,?,?,?,?,?,?,?,?,?)", [$account->id, NULL, $operator->id, uniqid(), uniqid(), -$balance, 'executed', 'migration set balance 0', \Carbon\Carbon::now(), \Carbon\Carbon::now()]);
                        $sum = DB::selectOne('select sum(amount) as amount from new_transactions where account_id=? GROUP BY `account_id`', [$account->id]);
                        $balance = $sum->amount;
                    }
                    DB::statement("insert into new_transactions (account_id,client_id,operator_id,track_number,bank_reference,amount,status,comment,created_at,updated_at) values (?,?,?,?,?,?,?,?,?,?)", [$account->id, NULL, $operator->id, uniqid(), uniqid(), $migrationBalance, 'executed', 'migration balance reloaded', \Carbon\Carbon::now(), \Carbon\Carbon::now()]);


                }

            }
        }
        Schema::dropIfExists('transactions');

        Schema::table('new_transactions', function (Blueprint $table) {
            $table->rename('transactions');
        });
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
