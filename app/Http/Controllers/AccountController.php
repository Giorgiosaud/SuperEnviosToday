<?php

    namespace App\Http\Controllers;

    use App\Account;
    use Illuminate\Http\Request;
    use Illuminate\Http\Response;

    class AccountController extends Controller
    {
        /**
         * Store a newly created resource in storage.
         *
         * @param Request $request
         *
         * @return Response
         */
        public function store(Request $request)
        {
            $validInputs = $request->validate([
                'user_id' => 'required|numeric',
                'bank_id' => 'required|numeric',
                'type' => 'in:corriente,ahorro',
                'number' => 'required|string',
            ]);
            $account = Account::create($validInputs);
            return $account;
        }

        /**
         * @param Request $request
         * @return mixed
         */
        public function storeOperatorAccount(Request $request)
        {
            $validInputs = $request->validate([
                'user_id' => 'required|numeric',
                'bank_id' => 'required|numeric',
                'number' => 'required|string',
                'type' => 'string',
            ]);
            $validInputs['is_operator_account']=true;
            $account = Account::create($validInputs);
            return $account;
            //
        }
    }
