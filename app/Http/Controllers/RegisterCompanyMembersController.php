<?php

namespace App\Http\Controllers;

use App\Role;
use Illuminate\Http\Request;

class RegisterCompanyMembersController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(){
        $roles=Role::all();
        return view('auth.registerMembers',['roles'=>$roles]);
    }
}
