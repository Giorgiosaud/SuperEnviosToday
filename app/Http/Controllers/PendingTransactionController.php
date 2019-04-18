<?php

    namespace App\Http\Controllers;

    use App\Account;
    use App\PendingTransaction;
    use App\User;
    use Illuminate\Http\Request;

    class PendingTransactionController extends Controller
    {
        public function index(){
            return view('coordinator.pendingTransactions');
        }
        public function indexAPI(Request $request)
        {

            $limit = $request->has('perPage') ? $request->get('perPage') : 20;
            $q = $request->has('q') ? $request->get('q') : null;
            if ($q) {
                return PendingTransaction::with(['client','receiver_account','operator_account'])
                    ->whereHas('client',function($query) use ($q){
                        $query->where('name', 'like', '%' . $q . '%')
                            ->orWhere('last_name', 'like', '%' . $q . '%')
                            ->orWhere('idn', 'like', '%' . $q . '%')
                            ->orWhere('idn_type', 'like', '%' . $q . '%')
                            ->orWhere('email', 'like', '%' . $q . '%');
                    })
                    ->orWhere('last_name', 'like', '%' . $q . '%')
                    ->orWhere('idn', 'like', '%' . $q . '%')
                    ->orWhere('idn_type', 'like', '%' . $q . '%')
                    ->orWhere('email', 'like', '%' . $q . '%')
                    ->orWhere('address', 'like', '%' . $q . '%')
                    ->orWhere('phone', 'like', '%' . $q . '%')

                    ->paginate($limit);

            }
            return PendingTransaction::with(['client','receiver_account','operator_account.owner'])->paginate($limit);
        }
        //TODO change endopoint if mytransaction is called from chileean transactions or add to previous method
        public function myTransactions(Request $request){
            if($request->user()->hasRole('coordinator')){

            $pendingTransactions=PendingTransaction::all();
            }
            else{
                $accountForeign=Account::find($request->foreign_account_id);
                $foreignUser=$accountForeign->owner;
                $pendingTransactions=PendingTransaction::whereHas('foreign_account_id',function($q) use ($foreignUser){
                    return $q->where('user_id',$foreignUser->id);
                })->get();
            }
            return view('operators.transactions-pending',$pendingTransactions);
        }
    }
