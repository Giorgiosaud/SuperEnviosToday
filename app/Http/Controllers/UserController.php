<?php

namespace App\Http\Controllers;

use App\Role;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $request = request();
        $users = User::with('roles');
        $roles=Role::all();
        $perPage = $request->has('perPage') ? $request->get('perPage') : config('app.paginated_by');

        return view('coordinator.user.index', ['users' => $users->paginate($perPage),'roles'=>$roles]);
        //
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     * */
    public function show(User $user)
    {
        $roles=Role::all();


        return view('coordinator.user.show', ['user' => $user,'roles'=>$roles]);
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, User $user)
    {
        $data = $this->validate($request, [
            'idn_type' => ['required', 'in:CI,PASSPORT,RUT,DNI,RIF'],
            'idn' => ['required', 'string', 'max:20'],
            'name' => ['required', 'string', 'max:255'],
            'last_name' => [ 'max:255'],
            'phone' => [ 'max:255'],
            'address' => [ 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'roles'=>['required','string']
        ]);
        $data['roles']=explode(',',$data['roles']);
        $user->fill($data);
        $user->save();
        $user->roles()->sync($data['roles']);
        $user->roles()->touch();
        return redirect(route('users.show',$user->id))->with('info',__('users.UPDATED:MESSAGE'));
    }


}
