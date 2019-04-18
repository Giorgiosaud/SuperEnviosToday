<?php

    namespace App\Http\Controllers;

    use App\Account;
    use App\Attachment;
    use App\Events\PendingTransactionAwaiting;
    use App\Events\TransactionExecuted;
    use App\PendingTransaction;
    use App\Rate;
    use App\Setting;
    use App\Transaction;
    use Carbon\Carbon;
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
            broadcast(new TransactionExecuted($request->user(),'made transaction'))->toOthers();
            return Transaction::create($validData);
        }

        /**
         * @param Request $request
         * @return RateController
         */
        public function normalstore(Request $request)
        {
            $validData = $request->validate([
                'client_id' => 'required|exists:users,id',
                'foreign_account_id' => 'required|exists:accounts,id',
                'received_transaction_attachment_ids.*' => 'numeric|exists:attachments,id',
                'receiver_account_id' => 'required|exists:accounts,id',
                'venezuelan_operator_account_id' => 'required|exists:accounts,id',
                'rate' => 'nullable|numeric',
                'amount' => 'required|numeric',
            ]);
            $venezuelan_account=Account::find($validData['venezuelan_operator_account_id']);

            $foreign_account = Account::find($validData['foreign_account_id']);
            $currency = $foreign_account->bank->currency;
            if (isset($validData['rate']) && $this->calculateRate($currency->id) !== $validData['rate']) {
                if ($request->user()->hasRole('coordinator')) {
                    $amountInBs = $validData['rate'] * $validData['amount'];
                } else {
                    $pending=PendingTransaction::create($validData);
                    broadcast(new PendingTransactionAwaiting($request->user()))->toOthers();
                    return $pending;
                }
            } else {
                $amountInBs = $this->calculateRate($currency->id) * $validData['amount'];
            }
            if($amountInBs > $venezuelan_account->balance){
                return abort(424,"No hay dinero disponible suficiente en la cuenta seleccionada");
            }
            $incomeTransactionData = [
                'client_id' => $validData['client_id'],
                'to_account_id' => $validData['foreign_account_id'],
                'amount' => $validData['amount'],
                'status' => 'confirmed',
                'type' => 'income',
            ];
            $incomeTransaction = Transaction::create($incomeTransactionData);
            if(isset($validData['received_transaction_attachment_ids'])) {
                foreach ($validData['received_transaction_attachment_ids'] as $attachmentId){
                    $attachment=Attachment::find($attachmentId);
                    $incomeTransaction->attachments()->save($attachment);
                }
            }
            $assignedTransactionData = [
                'related_transaction_id' => $incomeTransaction->id,
                'from_account_id' => $validData['venezuelan_operator_account_id'],
                'to_account_id' => $validData['receiver_account_id'],
                'amount' => $amountInBs,
                'status' => 'assigned',
                'type' => 'outcome',
            ];
            Transaction::create($assignedTransactionData);
            $set = Setting::where('key','venezuelanBankTax')->first();
            $taxVal=(float) str_replace(',','.',$set->value);
            if($taxVal!==0) {
                $amountTax = $amountInBs * $taxVal / 100;
                $venezuelanTax = [
                    'related_transaction_id' => $incomeTransaction->id,
                    'from_account_id' => null,
                    'to_account_id' => $validData['venezuelan_operator_account_id'],
                    'amount' => $amountTax,
                    'status' => 'terminated',
                    'type' => 'outcome',
                ];
                Transaction::create($venezuelanTax);
            }
            broadcast(new TransactionExecuted($request->user(),'made transaction'))->toOthers();
            return response('All transactions created',201);

        }

        private function calculateRate($currId)
        {
            return Rate::whereCurrencyId($currId)->where('since', '<=', Carbon::now())->orderBy('since', 'DESC')->first()->amount;
        }

    }
