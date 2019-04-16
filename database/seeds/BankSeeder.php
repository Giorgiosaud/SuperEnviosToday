<?php

    use App\Currency;
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
        $bs=Currency::where(['identificator'=>'Bs'])->first();
        $clp=Currency::where(['identificator'=>'CLP'])->first();
        factory(Bank::class)->create([
            'name'=>'Provincial',
            'currency_id'=>$bs->id
        ]);
        factory(Bank::class)->create([
            'name'=>'Mercantil',
            'currency_id'=>$bs->id
        ]);
        factory(Bank::class)->create([
            'name'=>'Venezuela',
            'currency_id'=>$bs->id
        ]);
        factory(Bank::class)->create([
            'name'=>'Banesco',
            'currency_id'=>$bs->id
        ]);
        factory(Bank::class)->create([
            'name'=>'Corp Banca',
            'currency_id'=>$bs->id
        ]);
        factory(Bank::class)->create([
            'name'=>'Del Sur',
            'currency_id'=>$bs->id
        ]);
        factory(Bank::class)->create([
            'name'=>'Banco Estado',
            'currency_id'=>$clp->id
        ]);
        factory(Bank::class)->create([
            'name'=>'Santander',
            'currency_id'=>$clp->id
        ]);
    }
}
