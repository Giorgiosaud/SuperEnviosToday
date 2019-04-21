<?php

    use Illuminate\Database\Seeder;

    class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(SettingsSeeder::class);
        $this->call(RoleSeeder::class);
        //     $this->call(UsersSeeder::class);
        //    $this->call(CurrencySeeder::class);
        //    $this->call(BankSeeder::class);
        //   $this->call(RateSeeder::class);
        //   $this->call(AccountsSeeder::class);
        //  $this->call(TransactionsSeeder::class);

    }
}
