<?php

use Illuminate\Database\Seeder;
use App\Bank;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Bank::class)->create([
            'name'=>'Provincial',
            'currency_id'=>"1"
        ]);
        factory(Bank::class)->create([
            'name'=>'Mercantil',
            'currency_id'=>"1"
        ]);
        factory(Bank::class)->create([
            'name'=>'Venezuela',
            'currency_id'=>"1"
        ]);
        factory(Bank::class)->create([
            'name'=>'Banesco',
            'currency_id'=>"1"
        ]);
        factory(Bank::class)->create([
            'name'=>'Corp Banca',
            'currency_id'=>"1"
        ]);
        factory(Bank::class)->create([
            'name'=>'Del Sur',
            'currency_id'=>"1"
        ]);
        factory(Bank::class)->create([
            'name'=>'Banco Estado',
            'currency_id'=>"2"
        ]);
        factory(Bank::class)->create([
            'name'=>'Santander',
            'currency_id'=>"2"
        ]);
        //
    }
}
