<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Role;
use App\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Class UserController.
 */
class UserController extends Controller
{
    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }

    /**
     * @return Factory|View
     */
    public function index()
    {
        $users = User::paginate(20);

        return view('coordinator.users', ['users' => $users]);
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function apiIndex(Request $request)
    {
        $limit = $request->has('perPage') ? $request->get('perPage') : 10;
        $q = $request->has('q') ? $request->get('q') : null;
        if ($q) {
            return User::where('name', 'like', '%' . $q . '%')
                ->orWhere('last_name', 'like', '%' . $q . '%')
                ->orWhere('idn', 'like', '%' . $q . '%')
                ->orWhere('idn_type', 'like', '%' . $q . '%')
                ->orWhere('email', 'like', '%' . $q . '%')
                ->orWhere('address', 'like', '%' . $q . '%')
                ->orWhere('phone', 'like', '%' . $q . '%')
                ->paginate($limit);
        }

        return User::paginate($limit);
    }

    /**
     * @param Request $request
     * @param User $user
     *
     * @return string
     */
    public function patch(UserRequest $request, User $user)
    {
        $validated = $request->validated();
        /* @noinspection PhpUndefinedMethodInspection */
        /* @noinspection PhpUndefinedFieldInspection */
        if (Auth::user()->id === $user->id && !in_array('coordinator', $request->roles)) {
            $roles = $request->roles;
            array_push($roles, 'coordinator');
        }
        /* @noinspection PhpUndefinedMethodInspection */
        if (Auth::user()->hasRole('coordinator')) {
            $user->update($validated);
            $user->syncRoles(collect($request->roles)->pluck('name_id'));

            return response([
                'success' => true,
                'message' => 'Se actualizarón los datos',
            ], 202);
        }

        return response([
            'success' => false,
            'message' => 'Unauthorized',
        ], 402);
    }

    public function myProfile()
    {
        return view('auth.profile');
    }

    /**
     * @return Factory|View
     */
    public function changePassword()
    {
        return view('auth.passwords.change');
    }

    /**
     * @param Request $request
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|confirmed|min:6',
        ]);
        $user = $request->user();
        $user->password = Hash::make($request->password);
        $user->setRememberToken(Str::random(60));
        $user->save();
        event(new PasswordReset($user));

        return redirect()->back()
            ->withInput($request->only('idn'))
            ->with('success', 'Cambio de Contraseña Exitoso');
    }

    /**
     * @return Authenticatable|null
     */
    public function info()
    {
        return \auth()->user();
    }

    /**
     * @param Request $request
     *
     * @return ResponseFactory|Response
     */
    public function infoPatch(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['string'],
            'address' => ['string'],
            'email' => ['string', 'email', 'max:255'],
        ]);
        $user = auth()->user();
        /* @noinspection PhpUndefinedMethodInspection */
        $user->update($validated);

        return response([
            'success' => true,
            'message' => 'Changes',
        ], 202);
    }

    public function userData(Request $request)
    {
        $validated = $request->validate([
            'idn' => 'required',
            'idn_type' => 'required|in:PASSPORT,RUT,CI,DNI',
        ]);

        return User::where($validated)->with('receivers')->get();
    }

    public function foreignOperators()
    {
        $users = User::whereHas(
            'roles',
            function ($q) {
                /* @noinspection PhpUndefinedMethodInspection */
                $q->where('name_id', 'coordinator')->orWhere('name_id', 'foreign_operator');
            }
        )->get();
        foreach ($users as $user) {
            foreach ($user->accounts as $account) {
                $account['Balance'] = $account->getBalanceAttribute();
            }
        }
        return $users;
    }

    public function operators()
    {
        $rolesId = ['foreign_operator', 'coordinator', 'venezuelan_operator'];
        $roles = Role::whereIn('name_id', $rolesId)->with('users')->get();

        $users = collect();
        foreach ($roles as $role) {
            foreach ($role->users as $user) {
                foreach ($user->accounts as $account) {
                    $account['Balance']=$account->getBalanceAttribute();
                }
            }
            $users->add($role->users);
        }
        return $users->flatten()->unique('id')->values();
        $users = User::with(['roles' => function ($query) use ($rolesId) {
            $query->whereIn('name_id', $rolesId);
        }])->get();
        return $users;
        foreach ($users as $user) {
            foreach ($user->accounts as $account) {
                // $account['Balance']=$account->getBalanceAttribute();
            }
        }
        return $users;
    }

    public function aliasing($id)
    {
        $this->guard()->user()->tokens->each(function ($token) {
            return $token->revoke();
        });
        $user = User::find($id);
        Auth::login($user);

        return redirect('/');
    }
}
