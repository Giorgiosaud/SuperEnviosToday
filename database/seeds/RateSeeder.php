<?php

use App\Rate;
use Illuminate\Database\Seeder;

class RateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currency=factory(\App\Currency::class)->create([
            'name'=>'Pesos Chilenos',
            'identificator'=>'CLP',
            'sign'=>'$',
        ]);
        factory(Rate::class,100)->create([
            'currency_id'=>$currency->id
        ]);
    }
}
