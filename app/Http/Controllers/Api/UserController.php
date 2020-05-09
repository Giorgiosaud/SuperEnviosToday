<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $request = request();
        $users = User::with('roles');
        $filters = ['name', 'last_name', 'idn', 'idn_type', 'email', 'address', 'phone', 'email_verified_at'];
        foreach ($filters as $filter) {
            if ($request->has($filter)) {
                $users->where($filter, 'like', '%' . $request[$filter] . '%');
            }
        }
        if ($request->has('roles')) {
            $roles=$request->roles;
            $users->whereHas('roles',function($query) use($roles){
                $query->whereIn('name_id',$roles);
            });
        }
        $perPage = $request->has('perPage') ? $request->get('perPage') : config('app.paginated_by');

        return $users->paginate($perPage);
    }

    public function search($idnType,$idn){
        $user= User::where('idn_type',$idnType)->where('idn',$idn)->first();
        if(!$user){
            return response('No user Founf',204);
        }
        return $user;
    }


}
