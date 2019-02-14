<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(){
        $users=User::paginate(20);
        return view('coordinator.users',['users' => $users]);
    }
    public function apiIndex(){
        return User::paginate(20);
    }
    //
}
