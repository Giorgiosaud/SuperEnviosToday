<?php

    use App\Currency;
    use Illuminate\Database\Seeder;

    class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Currency::class)->create([
            'name' => 'Bolivares Soberanos',
            'identificator'=>'Bs',
            'sign'=>'Bs S.',
        ]);
        factory(Currency::class)->create([
            'name'=>'Pesos Chilenos',
            'identificator'=>'CLP',
            'sign'=>'$',
        ]);
        //
    }
}
