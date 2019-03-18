<?php

    use App\Currency;
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
        factory(Currency::class)->create([
            'name'=>'Bolivar Soberano',
            'identificator'=>'Bs',
            'sign'=>'Bs S.',
        ]);
        $currency=factory(Currency::class)->create([
            'name'=>'Pesos Chilenos',
            'identificator'=>'CLP',
            'sign'=>'$',
        ]);
        factory(Rate::class,100)->create([
            'currency_id'=>$currency->id
        ]);
        $c3=factory(Currency::class)->create();
        $c4=factory(Currency::class)->create();
        factory(Rate::class,20)->create([
            'currency_id'=>$c3->id
        ]);
        factory(Rate::class,20)->create([
            'currency_id'=>$c4->id
        ]);}
}
