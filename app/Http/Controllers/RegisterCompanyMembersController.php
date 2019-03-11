<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Class RegisterCompanyMembersController
 * @package App\Http\Controllers
 */
class RegisterCompanyMembersController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(){
        return view('auth.registerMembers');
    }
    public function save(Request $request){
        $validated=$request->validate([
            'email'=>'required|email|confirmed',
            'address'=>'required|string|min:20',
            'phone'=>'required|string|min:9',
            'idn'=>'required',
            'idn_type'=>'required|in:PASSPORT,RUT,CI,DNI',
            'name'=>'required',
            'password'=>'required|string|min:6|confirmed|',
            'last_name'=>'required',
            'roles'=>'required',
        ]);


        $user=User::create([
            'email'=>$validated['email'],
            'address'=>$validated['address'],
            'phone'=>$validated['phone'],
            'idn'=>$validated['idn'],
            'idn_type'=>$validated['idn_type'],
            'name'=>$validated['name'],
            'password'=>Hash::make($validated['password']),
            'last_name'=>$validated['last_name'],
        ]);
        event(new Registered($user));
        foreach ($validated['roles'] as $role){
            $user->setRole($role);
        }
        return $user;

    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function saveClient(Request $request){
        $validated=$request->validate([
            'email'=>'required|email|confirmed',
            'address'=>'required|string|min:20',
            'phone'=>'required|string|min:9',
            'idn'=>'required',
            'idn_type'=>'required|in:PASSPORT,RUT,CI,DNI',
            'name'=>'required',
            'last_name'=>'required',
        ]);

        $validated['password']='secret';
        $user=User::create([
            'email'=>$validated['email'],
            'address'=>$validated['address'],
            'phone'=>$validated['phone'],
            'idn'=>$validated['idn'],
            'idn_type'=>$validated['idn_type'],
            'name'=>$validated['name'],
            'password'=>Hash::make($validated['password']),
            'last_name'=>$validated['last_name'],
        ]);
        event(new Registered($user));
        $user->setRole('receiver');
        return $user;

    }

}

