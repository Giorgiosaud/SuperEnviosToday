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
            $clp = Currency::where(['identificator'=>'CLP'])->first();
            factory(Rate::class, 100)->create([
            'currency_id'=> $clp->id,
        ]);
        }
    }
