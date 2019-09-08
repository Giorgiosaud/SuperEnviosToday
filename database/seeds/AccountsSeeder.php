<?php

    use App\Account;
    use App\Bank;
    use App\Role;
    use App\User;
    use Illuminate\Database\Seeder;

    class AccountsSeeder extends Seeder
    {
        /**
         * Run the database seeds.
         *
         * @return void
         */
        public function run()
        {
            //vanezuelan operator account
            $user = Role::find('venezuelan_operator')->users->first();
            $bank = Bank::whereName('Banesco')->first();
            $account=factory(Account::class)->create([
            'bank_id'             => $bank->id,
            'is_operator_account' => true,
        ]);
        $account->owners()->sync(['user_id'=>$user->id], false);
            //receiver account
            $user = User::where(['idn_type'=>'RIF', 'idn'=>'J-09513132-7'])->first();
            $account=factory(Account::class)->create([
            'bank_id'             => $bank->id,
            'is_operator_account' => false,
        ]);
        $account->owners()->sync(['user_id'=>$user->id], false);
        }
    }
