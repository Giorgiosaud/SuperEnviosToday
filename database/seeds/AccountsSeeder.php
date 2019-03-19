<?php

    use App\Bank;
    use App\Role;
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
        $user=Role::find('venezuelan_operator')->users->first();
        $bank=Bank::whereName('Provincial')->first();
        factory(\App\Account::class)->create([
            'bank_id'=>$bank->id,
            'user_id' => $user->id,
            'is_operator_account' => true,
        ]);
    }
}
