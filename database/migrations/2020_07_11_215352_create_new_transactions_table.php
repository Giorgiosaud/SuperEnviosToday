<?php

use Carbon\Carbon;
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
      $table->unsignedInteger('old_id')->nullable();
      $table->text('track_number')->nullable();
      $table->text('bank_reference')->nullable();
      $table->text('amount');
      $table->enum('status', ['executed', 'pending', 'in-progress']);
      $table->text('comment')->nullable();
      $table->timestamps();
      $table->softDeletes();
    });
    DB::statement("
                            INSERT INTO `new_transactions` (`account_id`,`client_id`,`operator_id`,`old_id`,`track_number`,`bank_reference`,`amount`,`status`,`created_at`,`updated_at`,`deleted_at`) 
                              SELECT `to_account_id` AS 'account_id', `client_id`,`to_user_id` AS 'operator_id',`id`,`id` AS 'track_number' , `transaction_number` AS 'bank_reference' , `amount`,'executed' AS `status`,`created_at`,`updated_at`,`deleted_at` 
                                FROM `transactions` 
                                  WHERE `client_id`=`from_user_id` 
                                    AND `type`='income'"
    );
    DB::statement("
                      INSERT INTO `new_transactions` (`account_id`,`client_id`,`operator_id`,`old_id`,`track_number`,`bank_reference`,`amount`,`status`,`created_at`,`updated_at`,`deleted_at`)
                        SELECT `from_account_id` AS 'account_id', NULL as client_id,`from_user_id` AS operator_id,`id`,`related_transaction_id` AS 'track_number' , `transaction_number` AS 'bank_reference' , `amount`*-1 as 'amount','executed' AS 'status', `created_at`,`updated_at`,`deleted_at` 
                          FROM `transactions` 
                            WHERE from_user_id IS NOT NULL 
                              AND type='outcome'"
    );

    DB::statement("
                        INSERT INTO `new_transactions` (`account_id`,`client_id`,`operator_id`,`old_id`,`track_number`,`bank_reference`,`amount`,`status`,`created_at`,`updated_at`,`deleted_at`) 
                          SELECT `to_account_id` AS 'account_id', `to_user_id` as 'client_id',`from_user_id` AS 'operator_id',`id`,`related_transaction_id` AS 'track_number' , `transaction_number` AS 'bank_reference' , `amount`,'executed' AS 'status', `created_at`,`updated_at`,`deleted_at` 
                            FROM `transactions` 
                              WHERE `from_user_id` IS NOT NULL 
                                AND `type`='outcome'"
    );
      DB::statement("
                        INSERT INTO `new_transactions` (`account_id`,`client_id`,`operator_id`,`track_number`,`bank_reference`,`amount`,`status`,`created_at`,`updated_at`,`deleted_at`) 
                          SELECT `to_account_id` AS 'account_id', NULL as 'client_id',`to_user_id` AS 'operator_id',`related_transaction_id` AS 'track_number' , `transaction_number` AS 'bank_reference' , `amount`*-1 as 'amount','executed' AS 'status', `created_at`,`updated_at`,`deleted_at` 
                            FROM `transactions` 
                              WHERE `from_user_id` IS NULL 
                                AND `type`='outcome' 
                                AND `transaction_number` IS NULL 
                                AND `amount` != 0 AND `related_transaction_id` IS NOT NULL"
    );

    $accounts = DB::table('accounts')->where('is_operator', true)->get();
    $operator = DB::selectOne("
                                SELECT `users`.`name`,`users`.`id`, `roles`.`name_id` 
                                  FROM `users` 
                                    JOIN `role_user` 
                                    ON `role_user`.`user_id`=users.id 
                                      JOIN `roles` 
                                      ON roles.`name_id`=`role_user`.`role_name_id` 
                                        WHERE name_id='coordinator'"
    );
    if ($accounts) {
      foreach ($accounts as $account) {
        $outcome = DB::selectOne("
                                        SELECT sum(`amount`) AS amount 
                                          FROM `transactions` 
                                            WHERE `from_account_id` = ? 
                                              AND `from_account_id` IS NOT NULL 
                                              AND `type` = 'outcome' 
                                                GROUP BY `from_account_id`
                                       ", [$account->id]
        );
        $income = DB::selectOne("
                                SELECT sum(`amount`) AS 'amount' 
                                  FROM `transactions` 
                                    WHERE `to_account_id` = ? 
                                      AND `to_account_id` IS NOT NULL 
                                      AND `type` = 'income' 
                                        GROUP BY `to_account_id`", [$account->id]
        );
        $incomeAmount = $income ? $income->amount : 0;
        $outcomeAmount = $outcome ? $outcome->amount : 0;
        $migrationBalance = intval(($incomeAmount - $outcomeAmount), 0);
        $sum = DB::selectOne("
                                    SELECT sum(`amount`) 
                                      AS 'amount' 
                                        FROM `new_transactions` 
                                          WHERE `account_id`=? 
                                            GROUP BY `account_id`", [$account->id]
        );
        if ($sum) {
          $balance = $sum->amount;
          while ($balance != 0) {
            DB::statement("
                            INSERT INTO `new_transactions` (`account_id`,`client_id`,`operator_id`,`track_number`,`bank_reference`,`amount`,`status`,`comment`,`created_at`,`updated_at`) 
                              VALUES (?,?,?,?,?,?,?,?,?,?)
                              ",
              [$account->id, NULL, $operator->id, uniqid(), uniqid(), -$balance, 'executed', 'migration set balance 0', Carbon::now(), Carbon::now()]
            );
            $sum = DB::selectOne("
                                        SELECT sum(`amount`) AS 'amount' 
                                          FROM `new_transactions` 
                                            WHERE `account_id`=? 
                                              GROUP BY `account_id`"
              , [$account->id]
            );
            $balance = $sum->amount;
          }
          DB::statement("    
                        INSERT INTO `new_transactions` (`account_id`,`client_id`,`operator_id`,`track_number`,`bank_reference`,`amount`,`status`,`comment`,`created_at`,`updated_at`) 
                          VALUES (?,?,?,?,?,?,?,?,?,?)",
            [$account->id, NULL, $operator->id, uniqid(), uniqid(), $migrationBalance, 'executed', 'migration balance reloaded', Carbon::now(), Carbon::now()]
          );
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
