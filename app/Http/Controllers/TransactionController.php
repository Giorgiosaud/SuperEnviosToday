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
            $validData['emitter_operator'] = $request->user()->id;

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
                'operator_account_id' => 'required|numeric',
                'to_account_id' => 'required|numeric',
                'from_account_id' => 'required|numeric',
                'from_client_id' => 'required|numeric',//TODO ADD LIMIT TO ACCOUNTS IN DB
                'amount' => 'required|numeric',
                'foreign_currency_id' => 'required|numeric'
            ]);
            $rate = Rate::whereCurrencyId($validData['foreign_currency_id'])->orderBy('since', 'DESC')->first();
            $transactionToOperator = [
                'amount' => $validData['amount'],
                'to_account_id' => $validData['operator_account_id'],
                'from_client_id' => $validData['from_client_id'],
                'foreign_currency_id' => $validData['from_client_id'],
                'status' => 'confirmed',
                'type' => 'income',
            ];
            $transactionToOperator = Transaction::create($transactionToOperator);
            $validData['amount'] = $rate['amount'] * $validData['amount'];
            $validData['transcaction_related'] = $transactionToOperator->id;
            $validData['status'] = 'assigned';
            //TODO agregar transaccion de impuesto por transferencia y calcularlo para deducirlo del monto a transferir (configurado desde settings)
            return Transaction::create($validData);
        }

    }
