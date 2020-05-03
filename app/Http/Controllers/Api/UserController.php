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

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
