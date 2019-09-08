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
            factory(Account::class)->create([
            'bank_id'             => $bank->id,
            'user_id'             => $user->id,
            'is_operator_account' => true,
        ]);
            //receiver account
            $user = User::where(['idn_type'=>'RIF', 'idn'=>'J-09513132-7'])->first();
            factory(Account::class)->create([
            'bank_id'             => $bank->id,
            'user_id'             => $user->id,
            'is_operator_account' => false,
        ]);
        }
    }
