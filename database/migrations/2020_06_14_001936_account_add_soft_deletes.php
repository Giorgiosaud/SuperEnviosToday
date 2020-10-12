<?php

  use App\Models\Account;
  use Illuminate\Database\Migrations\Migration;
  use Illuminate\Database\Schema\Blueprint;
  use Illuminate\Support\Facades\Schema;

  class AccountAddSoftDeletes extends Migration
  {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('accounts', function (Blueprint $table) {
        $table->softDeletes();
      });
      foreach ([28, 29] as $id) {
        $account = Account::find($id);
        if ($account) {
          $account->is_operator = true;
          $account->save();
        }
      }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::table('accounts', function (Blueprint $table) {
        $table->dropSoftDeletes();
      });
    }
  }
