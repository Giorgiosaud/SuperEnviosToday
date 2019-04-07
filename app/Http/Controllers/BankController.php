<?php

    namespace App\Http\Controllers;

    use App\Bank;
    use App\Currency;
    use Illuminate\Http\Request;

    class BankController extends Controller
    {
        /**
         * Display a listing of the resource.
         *
         * @return Bank[]|\Illuminate\Database\Eloquent\Collection
         */
        public function index()
        {
            return Bank::all();
            //
        }

        public function country_index(Currency $currency)
        {
            return $currency->banks;
        }

        public function venezuelan_index()
        {
            $currency = Currency::whereName('Bolivar Soberano')->first();
            return $currency->banks;
        }

        /**
         * Store a newly created resource in storage.
         *
         * @param \Illuminate\Http\Request $request
         *
         * @return Bank
         */
        public function store(Request $request)
        {
            $validated = $request->validate([
                'name' => 'required',
                'currency_id' => 'required'
            ]);
            $bank = Bank::create($validated);
            return $bank;
            //
        }


    }
