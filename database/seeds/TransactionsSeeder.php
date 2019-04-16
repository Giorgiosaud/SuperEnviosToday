<?php

    use App\Role;
    use App\Transaction;
    use Illuminate\Database\Seeder;

    class TransactionsSeeder extends Seeder
    {
        /**
         * Run the database seeds.
         *
         * @return void
         */
        public function run()
        {
            $venezuelan_operator = Role::find('venezuelan_operator')->users->first();
            factory(Transaction::class)->create([
                'to_account_id' => $venezuelan_operator->accounts->first()->id,
                'amount' => 2000000000,
                'type'=>'income',
                'status' => 'terminated',
            ]);

            //
        }
    }
