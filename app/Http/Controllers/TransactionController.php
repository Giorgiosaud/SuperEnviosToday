<?php

    namespace App\Http\Controllers;

    use App\Rate;
    use App\Transaction;
    use Illuminate\Http\Request;

    class TransactionController extends Controller
    {
        /**
         *
         * Display a listing of the resource.
         *
         * @return \Illuminate\Http\Response
         */
        public function index()
        {
            return view('operators.transactions');
            //
        }

        /**
         * Show the form for creating a new resource.
         *
         * @return \Illuminate\Http\Response
         */
        public function pending()
        {
            return view('operators.transactions-pending');
        }

        /**
         * Store a newly created resource in storage.
         *
         * @param \Illuminate\Http\Request $request
         *
         * @return \Illuminate\Http\Response
         */
        public function store(Request $request)
        {
            $validData = $request->validate([
                'to_account_id' => 'required|numeric',
                'amount' => 'required|numeric'
            ]);
            $validData['user_id'] = $request->user()->id;
            $validData['status'] = 'terminated';
            $validData['type'] = 'income';
            return Transaction::create($validData);
        }

        /**
         * @param Request $request
         * @return RateController
         */
        public function normalstore(Request $request)
        {
            $validData = $request->validate([
                'foreign_account_id' => 'required|numeric|exists:accounts,id',
                'receiver_account_id' => 'required|numeric|exists:accounts,id',
                'client_id' => 'required|numeric|exists:users,id',
                'venezuelan_operator_account_id' => 'required|numeric|exists:accounts,id',
                'rate'=>'required|numeric',
                'amount' => 'required|numeric'
            ]);
            // TODO analyze to make a pending for approoval transaction
            if($this->isPredefinedRate($validData['rate'],$validData['foreign_currency_id'])){
                //TODO make transaction confirmed
                $validData->only(['foreign_account_id','client_id','rate','amount']);


            }
            else{
                //TODO make transaction to confirm by a coordinator
            }

            return Transaction::create($validData);
        }

        private function isPredefinedRate($rate,$currId)
        {
            $calculatedDate = Rate::whereCurrencyId($currId)->orderBy('since', 'DESC')->first();
            return $calculatedDate===$rate;
        }

    }
