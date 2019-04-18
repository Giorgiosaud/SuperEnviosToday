<?php

    namespace App\Http\Controllers;

    use App\Role;
    use App\User;
    use Illuminate\Http\Request;

    class operatorsController extends Controller
    {
        public function index()
        {
            return view('coordinator.addFundsToOperator');
        }
        public function venezuelanList(){
            return view('operators.venezuelan-list');

        }
        public function venezuelanIndex()
        {
            return Role::whereName('Operador Venezolano')
                ->first()
                ->users()
                ->with('accounts')
                ->get();
        }
        //
    }
