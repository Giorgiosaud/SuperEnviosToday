<?php

    namespace App\Http\Controllers;

    use App\Account;
    use App\PendingTransaction;
    use App\User;
    use Doctrine\DBAL\Query\QueryBuilder;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;

    class PendingTransactionController extends Controller
    {
        public function index()
        {
            return view('coordinator.pendingTransactions');
        }

        public function indexAPI(Request $request)
        {

            $limit = $request->has('perPage') ? $request->get('perPage') : 20;
            $q = $request->has('q') ? $request->get('q') : null;
            if ($q) {
                return PendingTransaction::with(['client', 'receiver_account', 'operator_account.owner','foreign_account.owner'])
                    ->whereHas('client', function ($query) use ($q) {
                        return $query->where('name', 'like', '%' . $q . '%')
                            ->orWhere('last_name', 'like', '%' . $q . '%')
                            ->orWhere('idn', 'like', '%' . $q . '%')
                            ->orWhere('idn_type', 'like', '%' . $q . '%')
                            ->orWhere('email', 'like', '%' . $q . '%');
                    })
                    ->orWhereHas('receiver_account.bank', function ($query) use ($q) {
                        return $query->where('name', 'like', '%' . $q . '%');
                    })
                    ->orWhereHas('operator_account.owner', function ($query) use ($q) {
                        return $query->where('name', 'like', '%' . $q . '%')
                            ->orWhere('last_name', 'like', '%' . $q . '%');
                    })
                    ->orWhereHas('operator_account.bank', function ($query) use ($q) {
                        return $query->where('name', 'like', '%' . $q . '%');
                    })
                    ->orWhere('id', 'like', '%' . $q . '%')
                    ->paginate($limit);

            }
            return PendingTransaction::with(['client', 'receiver_account', 'operator_account.owner','foreign_account.owner'])->paginate($limit);
        }

        public function myTransactions()
        {
            return view('operators.transactions-pending');
        }

        public function myTransactionsAPI(Request $request)
        {
            $limit = $request->has('perPage') ? $request->get('perPage') : 20;
            $q = $request->has('q') ? $request->get('q') : null;
            if ($request->user()->hasRole('coordinator')) {
                $pendingTransactions = PendingTransaction::all();
            } else {
                $foreignUser = $request->user();
                $pendingTransactions = PendingTransaction::whereHas('foreign_account_id', function ($q) use ($foreignUser) {
                    return $q->where('user_id', $foreignUser->id);
                })->get();
            }
            return $pendingTransactions;
        }
    }
