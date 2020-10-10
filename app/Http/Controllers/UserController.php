<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        $request = request();
        $users = User::with('roles');
        $roles=Role::all();
        $perPage = $request->has('perPage') ? $request->get('perPage') : config('app.paginated_by');

        return view('coordinator.users.index', ['users' => $users->paginate($perPage),'roles'=>$roles]);
        //
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return Response
     * */
    public function show(User $user)
    {
        $roles=Role::all();
        $accounts=$user->accounts()->with('bank.currency')->get();
        $accounts->append('balance');
        return view('coordinator.users.show', compact('user','roles','accounts'));
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User $user
     * @return RedirectResponse|Redirector
     * @throws ValidationException
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

    /**
     * @param User $user
     */
    public function loginAs(User $user){
        Auth::login($user);
        return Redirect::back()->with('message','Operation Successful !');
    }

}
